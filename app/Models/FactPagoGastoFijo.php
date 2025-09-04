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
        'mes',
        'anio',
        'monto_pagado',
        'id_met_pago',      
        'comprobante',
        'fecha_pago',
        'turno'
    ];

    protected $casts = [
        'mes' => 'integer',
        'anio' => 'integer',
        'monto_pagado' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    const TURNO_DIA = 0;
    const TURNO_NOCHE = 1;
    
    const TURNOS = [
        self::TURNO_DIA => 'DÍA',
        self::TURNO_NOCHE => 'NOCHE'
    ];

    // Relación con el gasto fijo
    public function gastoFijo(): BelongsTo
    {
        return $this->belongsTo(FactGastoFijo::class, 'id_gasto_fijo', 'id_gasto_fijo');
    }

    // Relación con método de pago
    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }

    // AGREGAR ESTOS MÉTODOS:
    public function getTurnoTextoAttribute()
    {
        return self::TURNOS[$this->turno] ?? 'DESCONOCIDO';
    }

    public function getTurnoClaseAttribute()
    {
        return $this->turno === self::TURNO_DIA ? 'badge-warning' : 'badge-info';
    }

    public function getTurnoIconoAttribute()
    {
        return $this->turno === self::TURNO_DIA ? 'bx-sun' : 'bx-moon';
    }

    // Verificar si tiene comprobante
    public function getTieneComprobanteAttribute(): bool
    {
        return !empty($this->comprobante) && file_exists(storage_path('app/public/' . $this->comprobante));
    }

    // Obtener URL del comprobante
    public function getComprobanteUrlAttribute(): ?string
    {
        if ($this->comprobante) {
            return asset('storage/' . $this->comprobante);
        }
        return null;
    }

    // Obtener nombre del mes en español
    public function getNombreMesAttribute(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$this->mes] ?? 'Desconocido';
    }

    // Obtener el método de pago de forma rápida
    public function getMetPagoAttribute(): string
    {
        return $this->metodoPago ? $this->metodoPago->met_pago : 'Sin método';
    }

    public function scopeTurnoDia($query)
    {
        return $query->where('turno', self::TURNO_DIA);
    }

    public function scopeTurnoNoche($query)
    {
        return $query->where('turno', self::TURNO_NOCHE);
    }

    public function scopePorTurno($query, $turno)
    {
        return $query->where('turno', $turno);
    }

    // Scope para obtener pagos por método de pago
    public function scopePorMetodoPago($query, $idMetodoPago)
    {
        return $query->where('id_met_pago', $idMetodoPago);
    }

    // Scope para obtener pagos de un año específico 
    public function scopeDelAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    // Scope para obtener pagos de un mes específico
    public function scopeDelMes($query, $mes, $anio = null)
    {
        $query = $query->where('mes', $mes);
        
        if ($anio) {
            $query = $query->where('anio', $anio);
        }
        
        return $query;
    }

    // Método estático para formularios (al final, antes del cierre de clase)
    public static function getOpcionesTurno()
    {
        return self::TURNOS;
    }
}