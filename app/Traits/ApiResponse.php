<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respuesta JSON estándar para operaciones exitosas (200 OK).
     */
    protected function success($data = null, string $message = 'Operación exitosa.', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Respuesta JSON estándar para errores de cliente (4xx).
     */
    protected function error(string $message = 'Error en la solicitud.', int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Respuesta JSON específica for errores de validación (422).
     */
    protected function validationError(array $errors, string $message = 'Error de validación.'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }
}