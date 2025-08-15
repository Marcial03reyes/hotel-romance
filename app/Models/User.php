<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * AGREGAMOS 'role' aquí para permitir asignación masiva
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
        'role',  // ← NUEVA LÍNEA AGREGADA
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ========================================
    // NUEVOS MÉTODOS AGREGADOS PARA LOS ROLES
    // ========================================

    /**
     * Verificar si el usuario es administrador
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario es trabajador
     * 
     * @return bool
     */
    public function isTrabajador(): bool
    {
        return $this->role === 'trabajador';
    }

    /**
     * Verificar si el usuario tiene un rol específico
     * 
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Obtener el nombre del rol en español
     * 
     * @return string
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrador',
            'trabajador' => 'Trabajador',
            default => 'Sin rol'
        };
    }
}