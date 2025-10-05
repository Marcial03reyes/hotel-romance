<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactPagoGastoFijo extends Model
{
    protected $table = 'fact_pagos_gastos_fijos';
    protected $primaryKey = 'id_pago_gasto';

    protected $fillable = [
        'id_gasto_fijo',
        'monto_pagado',
        'id_met_pago',      
        'comprobante',
        'fecha_pago',
    ];

    protected $casts = [
        'monto_pagado' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    /**
     * Relación con el gasto fijo
     */
    public function gastoFijo(): BelongsTo
    {
        return $this->belongsTo(FactGastoFijo::class, 'id_gasto_fijo', 'id_gasto_fijo');
    }

    /**
     * Relación con método de pago
     */
    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }

    /**
     * Verificar si tiene comprobante
     */
    public function getTieneComprobanteAttribute(): bool
    {
        return !empty($this->comprobante) && file_exists(storage_path('app/public/' . $this->comprobante));
    }

    /**
     * Obtener URL del comprobante
     */
    public function getComprobanteUrlAttribute(): ?string
    {
        if ($this->comprobante) {
            return asset('storage/' . $this->comprobante);
        }
        return null;
    }

    /**
     * Obtener el método de pago de forma rápida
     */
    public function getMetPagoAttribute(): string
    {
        return $this->metodoPago ? $this->metodoPago->met_pago : 'Sin método';
    }

    /**
     * Obtener mes de la fecha de pago
     */
    public function getMesAttribute(): int
    {
        return $this->fecha_pago ? $this->fecha_pago->month : 0;
    }

    /**
     * Obtener año de la fecha de pago
     */
    public function getAnioAttribute(): int
    {
        return $this->fecha_pago ? $this->fecha_pago->year : 0;
    }

    /**
     * Obtener nombre del mes en español
     */
    public function getNombreMesAttribute(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$this->mes] ?? 'Desconocido';
    }

    /**
     * Scope para obtener pagos por método de pago
     */
    public function scopePorMetodoPago($query, $idMetodoPago)
    {
        return $query->where('id_met_pago', $idMetodoPago);
    }

    /**
     * Scope para obtener pagos de un año específico
     */
    public function scopeDelAnio($query, $anio)
    {
        return $query->whereYear('fecha_pago', $anio);
    }

    /**
     * Scope para obtener pagos de un mes específico
     */
    public function scopeDelMes($query, $mes, $anio = null)
    {
        $query = $query->whereMonth('fecha_pago', $mes);
        
        if ($anio) {
            $query = $query->whereYear('fecha_pago', $anio);
        }
        
        return $query;
    }

    /**
     * Scope para ordenar por fecha descendente
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_pago', 'desc');
    }
}