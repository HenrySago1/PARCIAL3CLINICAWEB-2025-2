<?php

namespace App\Filament\Resources\AiDiagnosisResource\Pages;

use App\Filament\Resources\AiDiagnosisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiDiagnosis extends EditRecord
{
    protected static string $resource = AiDiagnosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Ver'),
            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 