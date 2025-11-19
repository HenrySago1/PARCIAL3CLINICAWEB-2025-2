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
            // Devolvemos solo los campos que la App necesita
            $doctores = Doctor::select('id', 'name', 'specialty')->get();
            return $this->success($doctores, 'Doctores obtenidos correctamente.');

        } catch (\Exception $e) {
            return $this->error('No se pudo obtener la lista de doctores.', 500);
        }
    }
}