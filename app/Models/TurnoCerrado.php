<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoCerrado extends Model
{
    protected $table = 'turnos_cerrados';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'fecha',
        'turno',
        'cerrado_por',
        'observacion',
        'cerrado_en',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cerrado_en' => 'datetime',
    ];

    // Relación con el usuario que cerró el turno
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    // Verificar si una fecha+turno está cerrado
    public static function estaCerrado(string $fecha, int $turno): bool
    {
        return self::where('fecha', $fecha)
                   ->where('turno', $turno)
                   ->exists();
    }

    // Cerrar un turno
    public static function cerrar(string $fecha, int $turno, int $userId, ?string $observacion = null): self
    {
        return self::create([
            'fecha'      => $fecha,
            'turno'      => $turno,
            'cerrado_por'=> $userId,
            'cerrado_en' => now(),
            'observacion'=> $observacion,
        ]);
    }

    // Reabrir un turno (solo admin)
    public static function reabrir(string $fecha, int $turno): bool
    {
        return (bool) self::where('fecha', $fecha)
                          ->where('turno', $turno)
                          ->delete();
    }

    // Scope para buscar por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }
}