<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse; // <-- USA EL TRAIT

class AuthController extends Controller
{
    use ApiResponse; // <-- USA EL TRAIT

    /**
     * Tarea I.3: Login App Móvil
     */
    public function login(Request $request)
    {
        // 1. Validación
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Autenticar
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Credenciales inválidas.', 401);
        }

        $user = $request->user();

        // 3. Verificar si el usuario es un Paciente (Usando la relación)
        if (!$user->paciente) {
            Auth::guard('web')->logout(); 
            return $this->error('Acceso denegado: Solo para Pacientes.', 403);
        }

        // 4. Emitir el token de Sanctum
        $token = $user->createToken('app-movil-token')->plainTextToken;

        // 5. Respuesta exitosa
        return $this->success([
            'user' => $user->load('paciente'), // Carga los datos del paciente
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Inicio de sesión exitoso.');
    }

    /**
     * Cierra la sesión (logout)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Sesión cerrada correctamente.');
    }
}