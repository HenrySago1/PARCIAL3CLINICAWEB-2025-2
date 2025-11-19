<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AiDiagnosis;
use Illuminate\Support\Facades\Auth;

class GlaucomaVsNormalChart extends ChartWidget 
{
    protected static ?string $heading = 'Estadísticas de Diagnósticos (Ojos Analizados)';

    protected static ?string $pollingInterval = null; 
    
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // 1. Obtenemos todos los diagnósticos completados
        // (Si es doctor, solo los suyos; si es admin, todos)
        $query = AiDiagnosis::where('status', 'completado');
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDoctor()) {
             $query->where('doctor_id', $user->doctor->id);
        }
        
        $diagnoses = $query->get();

        // 2. Contamos manualmente en PHP (porque tenemos columnas separadas)
        $glaucomaCount = 0;
        $normalCount = 0;

        foreach ($diagnoses as $d) {
            // Ojo Derecho
            if (strtolower($d->result_right ?? '') === 'glaucoma') $glaucomaCount++;
            if (strtolower($d->result_right ?? '') === 'normal') $normalCount++;

            // Ojo Izquierdo
            if (strtolower($d->result_left ?? '') === 'glaucoma') $glaucomaCount++;
            if (strtolower($d->result_left ?? '') === 'normal') $normalCount++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ojos Analizados',
                    'data' => [$glaucomaCount, $normalCount], 
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.5)', // Rojo (Glaucoma)
                        'rgba(54, 162, 235, 0.5)', // Azul (Normal)
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Glaucoma', 'Normal'],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Cambiemos a 'pie' (pastel) o 'doughnut', se ve mejor para totales
        // O vuelve a 'bar' si prefieres barras
    }
}