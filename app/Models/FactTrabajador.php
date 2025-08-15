<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactTrabajador extends Model
{
    protected $table = 'fact_trabajadores';
    protected $primaryKey = 'DNI';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['DNI', 'nombre_apellido', 'sueldo', 'Fecha_inicio', 'Telef'];

    // Relación con horarios
    public function horarios()
    {
        return $this->hasMany(FactHorario::class, 'DNI', 'DNI');
    }

    // Obtener solo horarios activos
    public function horariosActivos()
    {
        return $this->hasMany(FactHorario::class, 'DNI', 'DNI')->where('activo', true);
    }
}