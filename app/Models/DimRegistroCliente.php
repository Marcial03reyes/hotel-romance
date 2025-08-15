<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimRegistroCliente extends Model
{
    protected $table = 'dim_registro_clientes';
    protected $primaryKey = 'doc_identidad';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['doc_identidad', 'nombre_apellido'];

    public function estadias()
    {
        return $this->hasMany(FactRegistroCliente::class, 'doc_identidad', 'doc_identidad');
    }
}