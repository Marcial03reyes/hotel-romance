<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FactRegistroCliente extends Model
{
    protected $table = 'fact_registro_clientes';
    protected $primaryKey = 'id_estadia';
    public $timestamps = false;

    protected $fillable = [
        'hora_ingreso',
        'hora_salida',      
        'fecha_ingreso',
        'habitacion',
        'doc_identidad',
        'obs',
        'turno'
    ];

    // Cast para manejo automático de fechas
    protected $casts = [
        'fecha_ingreso' => 'date'
    ];

    // Constantes para turnos (después de $casts)
    const TURNO_DIA = 0;
    const TURNO_NOCHE = 1;
    
    const TURNOS = [
        self::TURNO_DIA => 'DÍA',
        self::TURNO_NOCHE => 'NOCHE'
    ];

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(DimRegistroCliente::class, 'doc_identidad', 'doc_identidad');
    }

    // Relación con pago de habitación
    public function pagoHabitacion()
    {
        return $this->hasOne(FactPagoHab::class, 'id_estadia', 'id_estadia');
    }

    // Relación con pagos de productos
    public function pagosProductos()
    {
        return $this->hasMany(FactPagoProd::class, 'id_estadia', 'id_estadia');
    }

    // Accessors para turno (después de las relaciones)
    public function getTurnoTextoAttribute()
    {
        return self::TURNOS[$this->turno] ?? 'DESCONOCIDO';
    }

    // ACCESSOR: Formatear hora_ingreso como HH:mm AM/PM
    public function getHoraIngresoFormateadaAttribute()
    {
        if ($this->hora_ingreso) {
            return Carbon::createFromFormat('H:i:s', $this->hora_ingreso)->format('h:i A');
        }
        return null;
    }

    // ACCESSOR: Formatear hora_salida como HH:mm AM/PM
    public function getHoraSalidaFormateadaAttribute()
    {
        if ($this->hora_salida) {
            return Carbon::createFromFormat('H:i:s', $this->hora_salida)->format('h:i A');
        }
        return null;
    }

    // ACCESSOR: Formatear fecha de ingreso en español
    public function getFechaIngresoFormateadaAttribute()
    {
        if ($this->fecha_ingreso) {
            return $this->fecha_ingreso->format('d/m/Y');
        }
        return null;
    }

    // ACCESSOR: Formatear observaciones para mostrar (truncar si es muy largo)
    public function getObsFormateadaAttribute()
    {
        if ($this->obs) {
            return strlen($this->obs) > 50 
                ? substr($this->obs, 0, 50) . '...' 
                : $this->obs;
        }
        return null;
    }

    // MUTATOR: Limpiar observaciones antes de guardar
    public function setObsAttribute($value)
    {
        $this->attributes['obs'] = $value ? trim($value) : null;
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