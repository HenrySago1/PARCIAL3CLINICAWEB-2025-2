<?php

namespace App\Filament\Resources\ClinicalRecordResource\Pages;

use App\Filament\Resources\ClinicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Appointment; // <-- AÑADIDO

class CreateClinicalRecord extends CreateRecord
{
    protected static string $resource = ClinicalRecordResource::class;

    // --- TAREA II.4: MARCAR CITA COMO ATENDIDO ---
    protected function afterCreate(): void
    {
        // 'getRecord()' obtiene el Historial Clínico que acabamos de crear
        $record = $this->getRecord(); 

        // 1. Encontrar la cita asociada
        $appointment = Appointment::find($record->appointment_id);

        if ($appointment) {
            // 2. Actualizar el estado
            $appointment->status = 'atendido';
            $appointment->save();
        }
    }
}