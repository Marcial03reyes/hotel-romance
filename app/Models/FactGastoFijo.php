<?php
// app/Models/FactGastoFijo.php

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
     * Verificar si el servicio está pagado en un mes específico
     */
    public function estaPagado(int $mes, int $anio): bool
    {
        return $this->pagos()
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->exists();
    }

    /**
     * Obtener el pago de un mes específico
     */
    public function getPago(int $mes, int $anio): ?FactPagoGastoFijo
    {
        return $this->pagos()
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->first();
    }

    /**
     * Scope para servicios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}