<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactRegistroCliente extends Model
{
    protected $table = 'fact_registro_clientes';
    protected $primaryKey = 'id_estadia';
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(DimRegistroCliente::class, 'doc_identidad', 'doc_identidad');
    }

    public function pagosHabitacion()
    {
        return $this->hasMany(FactPagoHab::class, 'id_estadia');
    }

    public function pagosProductos()
    {
        return $this->hasMany(FactPagoProd::class, 'id_estadia');
    }
}
