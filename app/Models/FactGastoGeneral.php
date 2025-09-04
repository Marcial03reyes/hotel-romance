<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactGastoGeneral extends Model
{
    protected $table = 'fact_gastos_generales';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false;

    // Agregar 'turno' al fillable
    protected $fillable = [
        'id_tipo_gasto',
        'id_met_pago',
        'monto',
        'fecha_gasto',
        'comprobante', 
        'turno'  
    ];

    // Constantes para turnos (después de $fillable)
    const TURNO_DIA = 0;
    const TURNO_NOCHE = 1;
    
    const TURNOS = [
        self::TURNO_DIA => 'DÍA',
        self::TURNO_NOCHE => 'NOCHE'
    ];

    // Relación con tipo de gasto
    public function tipoGasto()
    {
        return $this->belongsTo(DimTipoGasto::class, 'id_tipo_gasto', 'id_tipo_gasto');
    }

    // Relación con método de pago
    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }

    // Accessors para turno (después de las relaciones)
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

    // Accessor para obtener el nombre del tipo de gasto
    public function getTipoAttribute()
    {
        return $this->tipoGasto ? $this->tipoGasto->nombre : 'Sin tipo';
    }

    // Accessor para obtener el método de pago
    public function getMetPagoAttribute()
    {
        return $this->metodoPago ? $this->metodoPago->met_pago : 'Sin método';
    }

    // Scopes para filtrar por turno (al final, antes del cierre de clase)
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

    // Método estático para formularios
    public static function getOpcionesTurno()
    {
        return self::TURNOS;
    }
}