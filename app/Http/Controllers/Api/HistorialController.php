<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\ClinicalRecord; // Asegúrate que el modelo se llame ClinicalRecord

class HistorialController extends Controller
{
    use ApiResponse;

    /**
     * Tarea II.3: Muestra el historial del paciente autenticado.
     */
    public function showMyHistorial(Request $request)
    {
        $patientId = $request->user()->paciente->id;

        // Asumo que el modelo se llama 'ClinicalRecord'
        // y que la relación en tu modelo Paciente se llama 'clinicalRecords'
        $historial = ClinicalRecord::where('patient_id', $patientId)
                                    ->with('doctor:id,name,specialty') // Asume relación con Doctor
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        if ($historial->isEmpty()) {
            return $this->success([], 'Aún no tienes registros en tu historial.');
        }

        return $this->success($historial, 'Historial obtenido correctamente.');
    }
}