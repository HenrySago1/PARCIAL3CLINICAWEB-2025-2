<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AiDiagnosisService
{
    private string $apiUrl;
    
    public function __construct()
    {
        // Asegúrate de que esta URL sea correcta (puerto 5000 para Python)
        // localhost para pruebas locales
        // $this->apiUrl = 'http://127.0.0.1:5000/api/prediccion';
        // $this->apiUrl = 'https://parcial3clinica-iadiagnosis-2025-2.onrender.com/api/prediccion';
        // local laravel y desplegado ia
        // $this->apiUrl = 'http://136.114.234.137:5000/api/prediccion';
        $this->apiUrl = env('AI_API_URL', 'http://127.0.0.1:5000/api/prediccion');
    }
    
    /**
     * MÉTODO PRINCIPAL: Analiza una imagen individual dada su ruta.
     * Este es el que usa el botón de Filament.
     */
    public function analyzeSingleImage(string $filePath): array
    {
        return $this->callApi($filePath) ?? ['error' => 'No se pudo conectar con la IA'];
    }

    /**
     * ALIAS: Por si tu código antiguo o el botón llaman a 'analyzeImage'.
     * Redirige al método correcto.
     */
    public function analyzeImage(string $filePath): array
    {
        return $this->analyzeSingleImage($filePath);
    }

    /**
     * Lógica interna para buscar el archivo y enviarlo a Python
     */
    private function callApi(string $filePath): ?array
    {
        try {
            $fullPath = null;

            // 1. Buscar en disco público (Si ya guardaste el registro)
            $publicPath = Storage::disk('public')->path($filePath);
            
            // 2. Buscar en temporal (Si estás creando el registro)
            $tempPath = storage_path('app/livewire-tmp/' . $filePath);

            if (file_exists($publicPath)) {
                $fullPath = $publicPath;
            } elseif (file_exists($tempPath)) {
                $fullPath = $tempPath;
            } elseif (file_exists($filePath)) {
                // A veces la ruta ya viene completa
                $fullPath = $filePath;
            }

            if (!$fullPath) {
                Log::error("IA: No encuentro la imagen en: $filePath");
                return ['error' => 'Archivo no encontrado. Guarda el registro e intenta de nuevo.'];
            }

            // Enviar a Python
            $response = Http::timeout(120)
                ->attach(
                    'imagen',
                    file_get_contents($fullPath),
                    basename($fullPath)
                )
                ->post($this->apiUrl);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("API Error Python: " . $response->body());
                return ['error' => 'Error en el servidor de IA'];
            }
        } catch (\Exception $e) {
            Log::error("Excepción llamando API: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}