<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Diagnosis API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la API de diagnóstico de glaucoma
    |
    */

    'api' => [
        // URL de tu API de diagnóstico
        'url' => env('AI_DIAGNOSIS_API_URL', 'http://127.0.0.1:5000/api/prediccion'),
        
        // Token de autenticación (si es necesario)
        'token' => env('AI_DIAGNOSIS_API_TOKEN', ''),
        
        // Timeout en segundos
        'timeout' => env('AI_DIAGNOSIS_API_TIMEOUT', 30),
        
        // Headers adicionales
        'headers' => [
            'Accept' => 'application/json',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing
    |--------------------------------------------------------------------------
    |
    | Configuración para el procesamiento de imágenes
    |
    */

    'image' => [
        // Formato de imagen preferido
        'format' => env('AI_DIAGNOSIS_IMAGE_FORMAT', 'base64'),
        
        // Tamaño máximo en MB
        'max_size' => env('AI_DIAGNOSIS_MAX_SIZE', 10),
        
        // Tipos de archivo permitidos
        'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp' , 'DICOM'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para cuando la API no está disponible
    |
    */

    'fallback' => [
        // Habilitar modo simulación cuando la API falla
        'enable_simulation' => env('AI_DIAGNOSIS_FALLBACK_SIMULATION', true),
        
        // Mensaje cuando la API no está disponible
        'message' => 'La API de diagnóstico no está disponible. Usando análisis simulado.',
    ],
]; 