<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimTipoGasto extends Model
{
    protected $table = 'dim_tipo_gasto';
    protected $primaryKey = 'id_tipo_gasto';
    public $timestamps = false;

    public function gastos()
    {
        return $this->hasMany(FactGastoGeneral::class, 'id_tipo_gasto');
    }
}
