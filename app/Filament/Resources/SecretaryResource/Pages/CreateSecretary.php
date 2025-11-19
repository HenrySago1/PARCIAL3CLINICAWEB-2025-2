<?php

namespace App\Filament\Resources\SecretaryResource\Pages;

use App\Filament\Resources\SecretaryResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateSecretary extends CreateRecord
{
    protected static string $resource = SecretaryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Crear el Usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'], // Ahora sí existe esta clave
            'password' => $data['password'], 
        ]);

        // 2. Asignar rol
        $user->assignRole('secretary');

        // 3. Vincular
        $data['user_id'] = $user->id;

        // 4. Limpiar (IMPORTANTE: Esto evita el error SQL en la tabla secretaries)
        unset($data['email'], $data['password']);

        return $data;
    }
}