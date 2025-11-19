<?php

namespace App\Filament\Resources\SecretaryResource\Pages;

use App\Filament\Resources\SecretaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSecretary extends EditRecord
{
    protected static string $resource = SecretaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
