<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FactGastoFijo extends Model
{
    protected $table = 'fact_gastos_fijos';
    protected $primaryKey = 'id_gasto_fijo';

    protected $fillable = [
        'nombre_servicio',
        'dia_vencimiento',
        'monto_fijo',
        'activo'
    ];

    protected $casts = [
        'dia_vencimiento' => 'integer',
        'monto_fijo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación con los pagos de gastos fijos
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(FactPagoGastoFijo::class, 'id_gasto_fijo', 'id_gasto_fijo');
    }

    /**
     * Obtener todos los pagos ordenados por fecha
     */
    public function getPagosOrdenados()
    {
        return $this->pagos()->orderBy('fecha_pago', 'desc')->get();
    }

    /**
     * Obtener el último pago realizado
     */
    public function getUltimoPago(): ?FactPagoGastoFijo
    {
        return $this->pagos()
            ->orderBy('fecha_pago', 'desc')
            ->first();
    }

    /**
     * Verificar si el servicio está pagado en un mes específico
     */
    public function estaPagadoEnMes(int $mes, int $anio): bool
    {
        return $this->pagos()
            ->whereMonth('fecha_pago', $mes)
            ->whereYear('fecha_pago', $anio)
            ->exists();
    }

    /**
     * Obtener el pago de un mes específico
     */
    public function getPagoDelMes(int $mes, int $anio): ?FactPagoGastoFijo
    {
        return $this->pagos()
            ->whereMonth('fecha_pago', $mes)
            ->whereYear('fecha_pago', $anio)
            ->first();
    }

    /**
     * Obtener total de pagos realizados
     */
    public function getTotalPagado(): float
    {
        return $this->pagos()->sum('monto_pagado');
    }

    /**
     * Obtener cantidad de pagos realizados
     */
    public function getCantidadPagos(): int
    {
        return $this->pagos()->count();
    }

    /**
     * Scope para servicios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para servicios inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }
}