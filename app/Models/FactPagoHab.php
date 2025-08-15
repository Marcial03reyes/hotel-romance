<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactPagoHab extends Model
{
    protected $table = 'fact_pago_hab';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    public function estadia()
    {
        return $this->belongsTo(FactRegistroCliente::class, 'id_estadia');
    }

    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago');
    }
}
