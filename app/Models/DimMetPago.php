<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimMetPago extends Model
{
    protected $table = 'dim_met_pago';
    protected $primaryKey = 'id_met_pago';
    public $timestamps = false;

    public function pagosHab()
    {
        return $this->hasMany(FactPagoHab::class, 'id_met_pago');
    }

    public function pagosProd()
    {
        return $this->hasMany(FactPagoProd::class, 'id_met_pago');
    }

    public function gastosGenerales()
    {
        return $this->hasMany(FactGastoGeneral::class, 'id_met_pago');
    }
}
