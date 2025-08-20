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
        'hora_salida',      // ← NUEVO CAMPO AGREGADO
        'fecha_ingreso',
        'habitacion',
        'doc_identidad'
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
}