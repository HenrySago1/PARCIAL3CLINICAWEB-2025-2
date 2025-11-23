<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Doctor;

class DoctorController extends Controller
{
    use ApiResponse;

    /**
     * Devuelve una lista de doctores para la App Móvil.
     */
    public function index()
    {
        try {
            // Devolvemos solo los campos que la App necesita, incluyendo horarios
            $doctores = Doctor::select('id', 'name', 'specialty', 'morning_start', 'morning_end', 'afternoon_start', 'afternoon_end')->get();
            return $this->success($doctores, 'Doctores obtenidos correctamente.');

        } catch (\Exception $e) {
            return $this->error('No se pudo obtener la lista de doctores.', 500);
        }
    }

    public function getAvailableSlots(Request $request, $id)
    {
        try {
            $doctor = Doctor::find($id);
            if (!$doctor) {
                return $this->error('Doctor no encontrado', 404);
            }

            $date = $request->query('date');
            if (!$date) {
                return $this->error('Fecha requerida (YYYY-MM-DD)', 400);
            }

            // 1. Generar todos los slots posibles
            $slots = [];
            $interval = 30; // minutos

            $periods = [
                ['start' => $doctor->morning_start, 'end' => $doctor->morning_end],
                ['start' => $doctor->afternoon_start, 'end' => $doctor->afternoon_end],
            ];

            foreach ($periods as $period) {
                // Asegurar que $date sea solo Y-m-d
                $dateOnly = \Carbon\Carbon::parse($date)->format('Y-m-d');
                $start = \Carbon\Carbon::parse($dateOnly . ' ' . $period['start']);
                $end = \Carbon\Carbon::parse($dateOnly . ' ' . $period['end']);

                while ($start < $end) {
                    $slots[] = $start->format('H:i'); // "08:00"
                    $start->addMinutes($interval);
                }
            }

            // 2. Obtener citas ocupadas
            $bookedTimes = \App\Models\Appointment::where('doctor_id', $id)
                ->whereDate('date', $date)
                ->where('status', '!=', 'cancelado') // Ignorar canceladas
                ->pluck('time') // "08:00:00"
                ->map(function ($time) {
                    return \Carbon\Carbon::parse($time)->format('H:i');
                })
                ->toArray();

            // 3. Filtrar
            $availableSlots = array_values(array_diff($slots, $bookedTimes));

            return $this->success($availableSlots, 'Horarios disponibles obtenidos.');

        } catch (\Exception $e) {
            return $this->error('Error al obtener horarios: ' . $e->getMessage(), 500);
        }
    }
}