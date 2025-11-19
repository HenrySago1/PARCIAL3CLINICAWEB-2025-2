<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Appointment; // Asegúrate que el modelo se llame Appointment
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CitaController extends Controller
{
    use ApiResponse;

    /**
     * Tarea II.3: Muestra las citas del paciente autenticado.
     */
    public function index(Request $request)
    {
        $patientId = $request->user()->paciente->id;

        $citas = Appointment::where('patient_id', $patientId)
                            ->with('doctor:id,name,specialty') // Asume que el modelo Doctor tiene 'name' y 'specialty'
                            ->orderBy('date', 'desc')
                            ->orderBy('time', 'desc')
                            ->get();

        return $this->success($citas, 'Citas obtenidas correctamente.');
    }

    /**
     * Tarea II.1: Almacena una nueva cita desde la App Móvil.
     */
    public function store(Request $request)
    {
        $user = $request->user(); 

        // 1. Validar
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i', // Ej: 14:30
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            // ----- ¡CAMBIO AQUÍ! -----
            // Añadimos ->toArray() para convertir el MessageBag en un array
            return $this->validationError($validator->errors()->toArray());
        }

        // 2. Verificar disponibilidad (Simplificado)
        $existing = Appointment::where('doctor_id', $request->doctor_id)
                                ->where('date', $request->date)
                                ->where('time', $request->time)
                                ->exists();

        if ($existing) {
            return $this->error('El horario seleccionado ya no está disponible.', 409); // Conflicto
        }
        
        // 3. Obtener ID del Paciente (Seguridad)
        $patientId = $user->paciente->id;

        // 4. Crear la cita
        try {
            $cita = Appointment::create([
                'patient_id' => $patientId,
                'doctor_id' => $request->doctor_id,
                'date' => $request->date,
                'time' => $request->time,
                'notes' => $request->notes,
                'status' => 'pendiente', // Inician como pendientes
            ]);

            return $this->success($cita, 'Cita reservada exitosamente.', 201); // Creado

        } catch (\Exception $e) {
            // Deberías loguear el error real
            // Log::error('Error al crear cita: ' . $e->getMessage());
            return $this->error('Ocurrió un error al guardar la cita.', 500);
        }
    }

    /**
     * Cancela una cita
     */
    public function destroy(Request $request, Appointment $cita)
    {
        $user = $request->user();

        if ($cita->patient_id !== $user->paciente->id) {
            return $this->error('No autorizado.', 403);
        }

        $cita->update(['status' => 'cancelado']);
        return $this->success(null, 'Cita cancelada correctamente.');
    }
}