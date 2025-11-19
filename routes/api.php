<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ARCHIVO DE PRUEBA DE API
|--------------------------------------------------------------------------
|
| Solo contiene una ruta para probar si este archivo se está cargando.
|
*/

// Esta ruta debería estar disponible en /api/test-api
Route::get('/test-api', function () {
    return response()->json([
        'message' => '¡routes/api.php SÍ está funcionando!'
    ]);
});

// Todas las demás rutas (/login, /doctors) están deshabilitadas temporalmente