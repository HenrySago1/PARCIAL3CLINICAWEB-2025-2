<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AiDiagnosis;
use Illuminate\Support\Facades\Auth;

class GlaucomaVsNormalChart extends ChartWidget 
{
    protected static ?string $heading = 'Estadísticas de Diagnósticos (Ojos Analizados)';

    protected static ?string $pollingInterval = null; 
    
    protected static ?int $sort = 2; // Orden 2 (después del saludo o stats generales)

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // 1. Obtenemos todos los diagnósticos completados
        // (Si es doctor, solo los suyos; si es admin, todos)
        $query = AiDiagnosis::where('status', 'completado');
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDoctor() && $user->doctor) {
    $query->where('doctor_id', $user->doctor->id);
} elseif ($user->isDoctor() && !$user->doctor) {
    // Si es doctor pero no tiene perfil, no mostrar nada para evitar error
    $query->whereRaw('1 = 0'); 
}
        
        $diagnoses = $query->get();

        // 2. Contamos manualmente en PHP
        $glaucomaCount = 0;
        $normalCount = 0;
        $cataratasCount = 0;

        foreach ($diagnoses as $d) {
            // Ojo Derecho
            $right = strtolower($d->result_right ?? '');
            if ($right === 'glaucoma') $glaucomaCount++;
            if ($right === 'normal') $normalCount++;
            if ($right === 'cataratas') $cataratasCount++;

            // Ojo Izquierdo
            $left = strtolower($d->result_left ?? '');
            if ($left === 'glaucoma') $glaucomaCount++;
            if ($left === 'normal') $normalCount++;
            if ($left === 'cataratas') $cataratasCount++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ojos Analizados',
                    'data' => [$normalCount, $glaucomaCount, $cataratasCount], 
                    'backgroundColor' => [
                        '#4CAF50', // Verde (Normal)
                        '#F44336', // Rojo (Glaucoma)
                        '#FF9800', // Naranja (Cataratas)
                    ],
                    'borderColor' => [
                        '#4CAF50',
                        '#F44336',
                        '#FF9800',
                    ],
                    'borderWidth' => 1,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Normal', 'Glaucoma', 'Cataratas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Gráfico de dona moderno
    }
}