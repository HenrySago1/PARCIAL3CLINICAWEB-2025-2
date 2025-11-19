<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\DoctorController;
use App\Models\Payment;

/*
|--------------------------------------------------------------------------
| Web Routes (Carga Principal)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| API Routes (Movidas aquí como "Plan C")
|--------------------------------------------------------------------------
| URL FINAL: /api/login (SIN /v1)
*/

Route::prefix('api')->group(function () {

    // --- 1. Ruta Pública (Autenticación) ---
    // URL: /api/login
    Route::post('/login', [AuthController::class, 'login']);

    // --- 2. Rutas Protegidas (Requiere Token de Sanctum) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn(Request $request) => $request->user()->load('paciente'));
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/doctors', [DoctorController::class, 'index']);

        Route::prefix('citas')->group(function () {
            Route::get('/', [CitaController::class, 'index']);
            Route::post('/', [CitaController::class, 'store']);
            Route::delete('/{cita}', [CitaController::class, 'destroy']);
        });
        Route::prefix('historiales')->group(function () {
            Route::get('/my', [HistorialController::class, 'showMyHistorial']);
        });

        // Ruta para imprimir recibo
        Route::get('/payment/{id}/pdf', function ($id) {
            $payment = Payment::with(['patient', 'service'])->findOrFail($id);
            return view('pdf.receipt', compact('payment'));
        })->name('payment.pdf')->middleware('auth');
    });
});
