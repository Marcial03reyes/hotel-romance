<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el intento de login
     */
    public function login(Request $request)
    {
        // Validar los campos
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'name.required' => 'El usuario es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        // Agregar remember si está marcado
        $remember = $request->boolean('remember');

        // Intentar autenticar
        if (Auth::attempt($credentials, $remember)) {
            // Regenerar la sesión por seguridad
            $request->session()->regenerate();

            // Redirigir al dashboard o a la página de registros
            return redirect()->intended(route('registros.index'));
        }

        // Si falla la autenticación
        throw ValidationException::withMessages([
            'name' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}