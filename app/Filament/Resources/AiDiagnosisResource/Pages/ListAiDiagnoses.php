<?php

namespace App\Filament\Resources\AiDiagnosisResource\Pages;

use App\Filament\Resources\AiDiagnosisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiDiagnoses extends ListRecords
{
    protected static string $resource = AiDiagnosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Diagnóstico'),
        ];
    }
} 