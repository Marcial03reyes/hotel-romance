<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DimProductoBodega extends Model
{
    protected $table = 'dim_productos_bodega';
    protected $primaryKey = 'id_prod_bod';
    public $timestamps = false;
    
    // IMPORTANTE: Agregar este array para permitir create()
    protected $fillable = [
        'nombre',
        'precio_actual',  
        'stock_inicial'  
    ];

    /**
     * Relación con las compras de bodega (productos para reventa)
     */
    public function comprasBodega()
    {
        return $this->hasMany(FactCompraBodega::class, 'id_prod_bod', 'id_prod_bod');
    }

    /**
     * Relación con las ventas a clientes
     */
    public function ventasClientes()
    {
        return $this->hasMany(FactPagoProd::class, 'id_prod_bod', 'id_prod_bod');
    }

    /**
     * Scope para obtener productos con estadísticas
     */
    public function scopeConEstadisticas($query)
    {
        return $query->leftJoin('fact_compra_bodega as fcb', 'dim_productos_bodega.id_prod_bod', '=', 'fcb.id_prod_bod')
            ->leftJoin('fact_pago_prod as fpp', 'dim_productos_bodega.id_prod_bod', '=', 'fpp.id_prod_bod')
            ->select(
                'dim_productos_bodega.id_prod_bod',
                'dim_productos_bodega.nombre',
                DB::raw('COALESCE(SUM(fcb.cantidad), 0) as unidades_compradas'),
                DB::raw('COALESCE(SUM(fpp.cantidad), 0) as unidades_vendidas'),
                DB::raw('COALESCE(SUM(fcb.cantidad), 0) - COALESCE(SUM(fpp.cantidad), 0) as stock'),
                DB::raw('COALESCE(SUM(fcb.cantidad * fcb.precio_unitario), 0) as inversion_total'),
                DB::raw('COUNT(DISTINCT fcb.id_compra_bodega) as total_compras'),
                DB::raw('MAX(fcb.fecha_compra) as ultima_compra')
            )
            ->groupBy('dim_productos_bodega.id_prod_bod', 'dim_productos_bodega.nombre');
    }

    /**
     * Atributo calculado: Stock actual
     */
    public function getStockAttribute()
    {
        $compradas = $this->comprasBodega()->sum('cantidad') ?? 0;
        $vendidas = $this->ventasClientes()->sum('cantidad') ?? 0;
        return $this->stock_inicial + $compradas - $vendidas;
    }

    /**
     * Atributo calculado: Unidades compradas totales
     */
    public function getUnidadesCompradasAttribute()
    {
        return $this->comprasBodega()->sum('cantidad') ?? 0;
    }

    /**
     * Atributo calculado: Unidades vendidas totales
     */
    public function getUnidadesVendidasAttribute()
    {
        return $this->ventasClientes()->sum('cantidad') ?? 0;
    }

    /**
     * Atributo calculado: Inversión total
     */
    public function getInversionTotalAttribute()
    {
        return $this->comprasBodega()->get()->sum(function($compra) {
            return $compra->cantidad * $compra->precio_unitario;
        }) ?? 0;
    }

    /**
     * Atributo calculado: Precio promedio de compra
     */
    public function getPrecioPromedioAttribute()
    {
        $unidades = $this->getUnidadesCompradasAttribute();
        $inversion = $this->getInversionTotalAttribute();
        
        return $unidades > 0 ? $inversion / $unidades : 0;
    }
}