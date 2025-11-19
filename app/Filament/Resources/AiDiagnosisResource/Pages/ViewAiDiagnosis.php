<?php

namespace App\Filament\Resources\AiDiagnosisResource\Pages;

use App\Filament\Resources\AiDiagnosisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAiDiagnosis extends ViewRecord
{
    protected static string $resource = AiDiagnosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }
} 