<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mostrar la página de configuración del perfil
     */
    public function show()
    {
        return view('settings.profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Actualizar el nombre del usuario
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name,' . Auth::id()],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Este nombre de usuario ya está en uso',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Nombre de usuario actualizado correctamente.');
    }

    /**
     * Actualizar la contraseña del usuario
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'current_password.current_password' => 'La contraseña actual es incorrecta',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.confirmed' => 'La confirmación de contraseña no coincide',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}