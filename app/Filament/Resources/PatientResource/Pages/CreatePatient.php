<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Crear el usuario (AHORA SÍ recibe $data['email'])
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], 
        ]);

        // 2. Asignar rol
        $user->assignRole('paciente');

        // 3. Vincular
        $data['user_id'] = $user->id;

        // 4. Limpiar (ESTO EVITA EL ERROR SQL)
        unset($data['email'], $data['password']);

        return $data;
    }
}