<?php

namespace App\Filament\Resources\AiDiagnosisResource\Pages;

use App\Filament\Resources\AiDiagnosisResource;
use App\Services\AiDiagnosisService;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Http\UploadedFile;

class AnalyzeAiDiagnosis extends Page
{
    protected static string $resource = AiDiagnosisResource::class;

    protected static string $view = 'filament.resources.ai-diagnosis-resource.pages.analyze-ai-diagnosis';

    public $record;
    public $analysisResult = '';
    public $isAnalyzing = true;
    public $analysisComplete = false;

    public function mount($record): void
    {
        $this->record = \App\Models\AiDiagnosis::findOrFail($record);
        $this->performAnalysis();
    }

    protected function performAnalysis(): void
    {
        $this->isAnalyzing = true;
        $this->analysisResult = '🔄 Iniciando análisis de la imagen...';

        $aiService = app(AiDiagnosisService::class);
        $aiService->updateDiagnosisStatus($this->record, 'analyzing');

        $rightResult = null;
        $leftResult = null;
        $errors = [];

        // Analizar ojo derecho si existe imagen
        if ($this->record->right_eye_image) {
            $rightPath = storage_path('app/public/' . $this->record->right_eye_image);
            if (!file_exists($rightPath)) {
                $rightPath = public_path('storage/' . $this->record->right_eye_image);
            }
            if (file_exists($rightPath)) {
                $uploadedFile = new UploadedFile(
                    $rightPath,
                    basename($this->record->right_eye_image),
                    mime_content_type($rightPath),
                    null,
                    true
                );
                try {
                    $rightResult = $aiService->analyzeImage($uploadedFile, $this->record->doctor_id);
                } catch (\Exception $e) {
                    $errors[] = 'Ojo derecho: ' . $e->getMessage();
                }
            } else {
                $errors[] = 'Ojo derecho: imagen no encontrada.';
            }
        }

        // Analizar ojo izquierdo si existe imagen
        if ($this->record->left_eye_image) {
            $leftPath = storage_path('app/public/' . $this->record->left_eye_image);
            if (!file_exists($leftPath)) {
                $leftPath = public_path('storage/' . $this->record->left_eye_image);
            }
            if (file_exists($leftPath)) {
                $uploadedFile = new UploadedFile(
                    $leftPath,
                    basename($this->record->left_eye_image),
                    mime_content_type($leftPath),
                    null,
                    true
                );
                try {
                    $leftResult = $aiService->analyzeImage($uploadedFile, $this->record->doctor_id);
                } catch (\Exception $e) {
                    $errors[] = 'Ojo izquierdo: ' . $e->getMessage();
                }
            } else {
                $errors[] = 'Ojo izquierdo: imagen no encontrada.';
            }
        }

        // Guardar resultados
        $this->record->right_eye_result = $rightResult;
        $this->record->left_eye_result = $leftResult;
        $this->record->save();

        // Determinar estado final
        if ($rightResult || $leftResult) {
            $aiService->updateDiagnosisStatus($this->record, 'completed');
            $this->analysisResult = '';
            if ($rightResult) {
                $this->analysisResult .= "Ojo derecho: $rightResult\n";
            }
            if ($leftResult) {
                $this->analysisResult .= "Ojo izquierdo: $leftResult\n";
            }
            if ($errors) {
                $this->analysisResult .= "\nErrores:\n" . implode("\n", $errors);
            }
        } else {
            $this->analysisResult = '❌ Error: No se encontró ninguna imagen para analizar. ' . (count($errors) ? implode(' ', $errors) : '');
            $aiService->updateDiagnosisStatus($this->record, 'error', $this->analysisResult);
        }

        $this->analysisComplete = true;
        $this->isAnalyzing = false;
        $this->record->refresh();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_list')
                ->label('Ver Lista de Diagnósticos')
                ->url($this->getResource()::getUrl('index'))
                ->color('primary')
                ->icon('heroicon-o-list-bullet'),
        ];
    }

    public function getTitle(): string
    {
        return 'Analizando Diagnóstico AI';
    }
} 