<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DimProductoHotel extends Model
{
    protected $table = 'dim_productos_hotel';
    protected $primaryKey = 'id_prod_hotel';
    public $timestamps = false;
    
    // IMPORTANTE: Agregar $fillable para permitir create()
    protected $fillable = ['nombre'];

    /**
     * Relación con las compras internas
     */
    public function comprasInternas()
    {
        return $this->hasMany(FactCompraInterna::class, 'id_prod_bod', 'id_prod_hotel');
    }

    /**
     * Scope para obtener productos con estadísticas
     */
    public function scopeConEstadisticas($query)
    {
        return $query->leftJoin('fact_compra_interna as fci', 'dim_productos_hotel.id_prod_hotel', '=', 'fci.id_producto')
            ->select(
                'dim_productos_hotel.id_prod_hotel',
                'dim_productos_hotel.nombre',
                DB::raw('COALESCE(SUM(fci.cantidad), 0) as total_comprado'),
                DB::raw('COALESCE(SUM(fci.cantidad * fci.precio_unitario), 0) as inversion_total'),
                DB::raw('COUNT(DISTINCT fci.id_compra) as total_compras'),
                DB::raw('MAX(fci.created_at) as ultima_compra'),
                DB::raw('MIN(fci.created_at) as primera_compra')
            )
            ->groupBy('dim_productos_hotel.id_prod_hotel', 'dim_productos_hotel.nombre');
    }

    /**
     * Atributo calculado: Total de unidades compradas
     */
    public function getTotalCompradoAttribute()
    {
        return $this->comprasInternas()->sum('cantidad') ?? 0;
    }

    /**
     * Atributo calculado: Inversión total
     */
    public function getInversionTotalAttribute()
    {
        return $this->comprasInternas()->get()->sum(function($compra) {
            return $compra->cantidad * $compra->precio_unitario;
        }) ?? 0;
    }

    /**
     * Atributo calculado: Frecuencia de compra (días promedio entre compras)
     */
    public function getFrecuenciaCompraAttribute()
    {
        $compras = $this->comprasInternas()
            ->orderBy('created_at')
            ->pluck('created_at')
            ->toArray();

        if (count($compras) < 2) {
            return null; // No hay suficientes datos
        }

        $totalDias = 0;
        $intervalos = 0;

        for ($i = 1; $i < count($compras); $i++) {
            $fechaAnterior = Carbon::parse($compras[$i-1]);
            $fechaActual = Carbon::parse($compras[$i]);
            $totalDias += $fechaAnterior->diffInDays($fechaActual);
            $intervalos++;
        }

        return $intervalos > 0 ? round($totalDias / $intervalos, 1) : null;
    }

    /**
     * Atributo calculado: Días desde última compra
     */
    public function getDiasDesdeUltimaCompraAttribute()
    {
        $ultimaCompra = $this->comprasInternas()
            ->orderByDesc('created_at')
            ->first();

        if (!$ultimaCompra) {
            return null;
        }

        return Carbon::parse($ultimaCompra->created_at)->diffInDays(now());
    }

    /**
     * Atributo calculado: Estado del indicador de recompra
     */
    public function getIndicadorRecompraAttribute()
    {
        $frecuencia = $this->getFrecuenciaCompraAttribute();
        $diasDesdeUltima = $this->getDiasDesdeUltimaCompraAttribute();

        if (!$frecuencia || !$diasDesdeUltima) {
            return [
                'estado' => 'sin_datos',
                'mensaje' => 'Sin datos suficientes',
                'color' => 'gray',
                'icono' => 'bx-question-mark'
            ];
        }

        $porcentaje = ($diasDesdeUltima / $frecuencia) * 100;

        if ($porcentaje < 50) {
            return [
                'estado' => 'recien_comprado',
                'mensaje' => 'Recién comprado',
                'porcentaje' => $porcentaje,
                'dias_restantes' => max(0, $frecuencia - $diasDesdeUltima),
                'color' => 'green',
                'icono' => 'bx-check-circle'
            ];
        } elseif ($porcentaje < 80) {
            return [
                'estado' => 'bueno',
                'mensaje' => 'En buen estado',
                'porcentaje' => $porcentaje,
                'dias_restantes' => max(0, $frecuencia - $diasDesdeUltima),
                'color' => 'blue',
                'icono' => 'bx-time'
            ];
        } elseif ($porcentaje < 100) {
            return [
                'estado' => 'proximo_a_agotar',
                'mensaje' => 'Próximo a recomprar',
                'porcentaje' => $porcentaje,
                'dias_restantes' => max(0, $frecuencia - $diasDesdeUltima),
                'color' => 'yellow',
                'icono' => 'bx-error'
            ];
        } else {
            return [
                'estado' => 'requiere_compra',
                'mensaje' => 'Requiere recompra',
                'porcentaje' => $porcentaje,
                'dias_excedidos' => $diasDesdeUltima - $frecuencia,
                'color' => 'red',
                'icono' => 'bx-error-circle'
            ];
        }
    }

    /**
     * Atributo calculado: Precio promedio de compra
     */
    public function getPrecioPromedioAttribute()
    {
        $totalUnidades = $this->getTotalCompradoAttribute();
        $inversionTotal = $this->getInversionTotalAttribute();
        
        return $totalUnidades > 0 ? $inversionTotal / $totalUnidades : 0;
    }
}