<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactPagoProd extends Model
{
    protected $table = 'fact_pago_prod';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    public function estadia()
    {
        return $this->belongsTo(FactRegistroCliente::class, 'id_estadia');
    }

    public function producto()
    {
        return $this->belongsTo(DimProductoBodega::class, 'id_prod_bod');
    }

    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago');
    }
}
