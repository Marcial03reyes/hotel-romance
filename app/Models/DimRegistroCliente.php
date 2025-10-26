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

    protected $fillable = [
        'doc_identidad', 
        'nombre_apellido',
        'estado_civil',     
        'fecha_nacimiento',  
        'lugar_nacimiento',
        'nacionalidad',
        'sexo',                    
        'profesion_ocupacion'   
    ];

    // Cast para convertir fecha_nacimiento a Carbon automáticamente
    protected $casts = [
        'fecha_nacimiento' => 'date'
    ];

    public function estadias()
    {
        return $this->hasMany(FactRegistroCliente::class, 'doc_identidad', 'doc_identidad');
    }

    // ACCESSOR: Formatear fecha de nacimiento en español
    public function getFechaNacimientoFormateadaAttribute()
    {
        if ($this->fecha_nacimiento) {
            return $this->fecha_nacimiento->format('d/m/Y');
        }
        return null;
    }

    // ACCESSOR: Calcular edad basada en fecha de nacimiento
    public function getEdadAttribute()
    {
        if ($this->fecha_nacimiento) {
            return $this->fecha_nacimiento->age;
        }
        return null;
    }

    // ACCESSOR: Formatear estado civil para mostrar
    public function getEstadoCivilFormateadoAttribute()
    {
        if ($this->estado_civil) {
            return ucfirst(strtolower($this->estado_civil));
        }
        return null;
    }

    // MUTATOR: Normalizar estado civil antes de guardar
    public function setEstadoCivilAttribute($value)
    {
        $this->attributes['estado_civil'] = $value ? ucfirst(strtolower(trim($value))) : null;
    }

    // MUTATOR: Normalizar lugar de nacimiento antes de guardar
    public function setLugarNacimientoAttribute($value)
    {
        $this->attributes['lugar_nacimiento'] = $value ? ucwords(strtolower(trim($value))) : null;
    }
}