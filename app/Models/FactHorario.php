<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactHorario extends Model
{
    protected $table = 'fact_horarios';
    
    protected $fillable = [
        'DNI',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'activo' => 'boolean'
    ];

    // Relación con trabajador
    public function trabajador()
    {
        return $this->belongsTo(FactTrabajador::class, 'DNI', 'DNI');
    }

    // Scope para obtener solo horarios activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}