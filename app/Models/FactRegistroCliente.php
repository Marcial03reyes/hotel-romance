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
        'turno',
        'fecha_ingreso_real',
        'hora_ingreso_real',
        'ciudad_procedencia',      
        'ciudad_destino',          
        'motivo_viaje',           
        'placa_vehiculo'
    ];

    // Cast para manejo automático de fechas
    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_ingreso_real' => 'date'
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

    // Accessors para turno
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

    // NUEVOS ACCESSORS PARA CAMPOS AUXILIARES

    // ACCESSOR: Formatear hora_ingreso_real como HH:mm AM/PM
    public function getHoraIngresoRealFormateadaAttribute()
    {
        if ($this->hora_ingreso_real) {
            return Carbon::createFromFormat('H:i:s', $this->hora_ingreso_real)->format('h:i A');
        }
        return null;
    }

    // ACCESSOR: Formatear fecha_ingreso_real en español
    public function getFechaIngresoRealFormateadaAttribute()
    {
        if ($this->fecha_ingreso_real) {
            return $this->fecha_ingreso_real->format('d/m/Y');
        }
        return null;
    }

    // ACCESSOR: Decidir qué fecha mostrar (real si existe, sino normal)
    public function getFechaDisplayAttribute()
    {
        return $this->fecha_ingreso_real ? $this->fecha_ingreso_real : $this->fecha_ingreso;
    }

    // ACCESSOR: Decidir qué fecha mostrar formateada
    public function getFechaDisplayFormateadaAttribute()
    {
        $fecha = $this->fecha_display;
        return $fecha ? $fecha->format('d/m/Y') : null;
    }

    // ACCESSOR: Decidir qué hora mostrar (real si existe, sino normal)
    public function getHoraDisplayAttribute()
    {
        return $this->hora_ingreso_real ?: $this->hora_ingreso;
    }

    // ACCESSOR: Decidir qué hora mostrar formateada
    public function getHoraDisplayFormateadaAttribute()
    {
        $hora = $this->hora_display;
        if ($hora) {
            return Carbon::createFromFormat('H:i:s', $hora)->format('h:i A');
        }
        return null;
    }

    // MUTATOR: Limpiar observaciones antes de guardar
    public function setObsAttribute($value)
    {
        $this->attributes['obs'] = $value ? trim($value) : null;
    }

    // MÉTODOS HELPER PARA LÓGICA DE NEGOCIO

    // Verificar si es turno noche
    public function esTurnoNoche()
    {
        return $this->turno == self::TURNO_NOCHE;
    }

    // Verificar si es turno día
    public function esTurnoDia()
    {
        return $this->turno == self::TURNO_DIA;
    }

    // Verificar si tiene campos auxiliares llenos
    public function tieneHorarioReal()
    {
        return !is_null($this->fecha_ingreso_real) && !is_null($this->hora_ingreso_real);
    }

    // Verificar si debe mostrar campos auxiliares (turno noche)
    public function deberiaUsarCamposAuxiliares()
    {
        return $this->esTurnoNoche();
    }

    // Scopes para filtrar por turno
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

    // Scope para filtrar registros con horario real
    public function scopeConHorarioReal($query)
    {
        return $query->whereNotNull('fecha_ingreso_real')
                    ->whereNotNull('hora_ingreso_real');
    }

    // Método estático para formularios
    public static function getOpcionesTurno()
    {
        return self::TURNOS;
    }
}