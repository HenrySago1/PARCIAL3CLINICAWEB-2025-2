<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AiDiagnosisService;
use Illuminate\Http\UploadedFile;

class TestAiApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test-api {--image= : Ruta a una imagen de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la conexión con la API de diagnóstico AI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando conexión con API de diagnóstico AI...');
        
        $apiUrl = config('ai_diagnosis.api.url');
        $this->info("URL de la API: {$apiUrl}");
        
        // Verificar si se proporcionó una imagen de prueba
        $imagePath = $this->option('image');
        
        if (!$imagePath) {
            $this->warn('No se proporcionó imagen de prueba. Solo probando conectividad...');
            
            try {
                // Intentar con POST vacío para verificar conectividad
                $response = \Illuminate\Support\Facades\Http::timeout(5)->post($apiUrl, []);
                if ($response->successful()) {
                    $this->info('✅ API está respondiendo correctamente');
                } else {
                    $this->warn('⚠️  API respondió con código: ' . $response->status());
                    $this->info('Esto es normal si la API requiere una imagen para funcionar');
                    $this->info('Respuesta: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->error('❌ No se pudo conectar con la API: ' . $e->getMessage());
                $this->info('Verifica que tu API esté ejecutándose en: ' . $apiUrl);
                $this->info('Asegúrate de que Flask esté corriendo con: python tu_archivo.py');
            }
            
            return;
        }
        
        // Probar con imagen real
        if (!file_exists($imagePath)) {
            $this->error("❌ La imagen no existe: {$imagePath}");
            return;
        }
        
        $this->info("📸 Probando con imagen: {$imagePath}");
        
        try {
            // Crear un UploadedFile simulado
            $uploadedFile = new UploadedFile(
                $imagePath,
                basename($imagePath),
                mime_content_type($imagePath),
                null,
                true
            );
            
            $aiService = new AiDiagnosisService();
            $result = $aiService->analyzeImage($uploadedFile, 1);
            
            $this->info('✅ Análisis completado exitosamente');
            $this->newLine();
            $this->info('📋 Resultado del análisis:');
            $this->line($result);
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante el análisis: ' . $e->getMessage());
        }
    }
}
