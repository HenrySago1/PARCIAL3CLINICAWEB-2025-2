<?php

namespace App\Filament\Resources\AiDiagnosisResource\Pages;

use App\Filament\Resources\AiDiagnosisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiDiagnosis extends CreateRecord
{
    protected static string $resource = AiDiagnosisResource::class;

    // --- ESTA FUNCIÓN CAMBIA LA REDIRECCIÓN ---
    protected function getRedirectUrl(): string
    {
        // En lugar de ir a la lista, vamos a la vista de DETALLE del registro creado
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}