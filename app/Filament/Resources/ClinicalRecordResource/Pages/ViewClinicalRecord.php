<?php

namespace App\Filament\Resources\ClinicalRecordResource\Pages;

use App\Filament\Resources\ClinicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClinicalRecord extends ViewRecord
{
    protected static string $resource = ClinicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }
} 