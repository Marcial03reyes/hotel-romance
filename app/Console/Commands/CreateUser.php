<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * Sintaxis: user:create {nombre} {contraseña} {rol=trabajador}
     * El rol es opcional y por defecto será "trabajador"
     *
     * @var string
     */
    protected $signature = 'user:create {name} {password} {role=trabajador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un nuevo usuario con rol específico (admin o trabajador)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener los argumentos del comando
        $name = $this->argument('name');
        $password = $this->argument('password');
        $role = $this->argument('role');

        // Mostrar información de lo que se va a crear
        $this->info("Creando usuario: {$name}");
        $this->info("Rol: {$role}");

        // Validar que el rol sea válido
        if (!in_array($role, ['admin', 'trabajador'])) {
            $this->error('❌ Error: El rol debe ser "admin" o "trabajador"');
            $this->line('Ejemplo: php artisan user:create Juan mipassword admin');
            return 1; // Código de error
        }

        // Verificar si el usuario ya existe
        if (User::where('name', $name)->exists()) {
            $this->error("❌ Error: Ya existe un usuario con el nombre '{$name}'");
            return 1; // Código de error
        }

        // Validar que la contraseña tenga al menos 6 caracteres
        if (strlen($password) < 6) {
            $this->error('❌ Error: La contraseña debe tener al menos 6 caracteres');
            return 1; // Código de error
        }

        try {
            // Crear el usuario
            $user = User::create([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
            ]);

            // Mostrar mensaje de éxito
            $this->info('✅ ¡Usuario creado exitosamente!');
            $this->line('');
            $this->line('📋 Detalles del usuario:');
            $this->line("   Nombre: {$user->name}");
            $this->line("   Rol: {$user->role} ({$user->role_name})");
            $this->line("   ID: {$user->id}");
            $this->line('');
            $this->line('🔑 Credenciales de acceso:');
            $this->line("   Usuario: {$name}");
            $this->line("   Contraseña: {$password}");

            return 0; // Código de éxito

        } catch (\Exception $e) {
            $this->error('❌ Error al crear el usuario: ' . $e->getMessage());
            return 1; // Código de error
        }
    }
}