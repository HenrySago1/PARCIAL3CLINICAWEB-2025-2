<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User; // Importar User
use Illuminate\Support\Facades\Hash; // Importar Hash

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    // --- AQUÍ ES DONDE DEBE IR EL HOOK ---
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Crear el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], 
        ]);

        // 2. Asignar rol
        $user->assignRole('doctor');

        // 3. Vincular
        $data['user_id'] = $user->id;

        // 4. Limpiar (ESTO EVITA EL ERROR SQL)
        unset($data['email'], $data['password']);

        return $data;
    }
}