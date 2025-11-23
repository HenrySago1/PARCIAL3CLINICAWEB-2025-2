<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;

class TopDoctorsChart extends ChartWidget
{
    protected static ?string $heading = 'Doctores más solicitados';
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $topDoctors = Appointment::selectRaw('doctor_id, COUNT(*) as total')
            ->groupBy('doctor_id')
            ->orderByDesc('total')
            ->with('doctor')
            ->limit(5)
            ->get();

        $labels = $topDoctors->map(fn($a) => optional($a->doctor)->name ?: 'Sin doctor')->toArray();
        $data = $topDoctors->map(fn($a) => $a->total)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Citas asignadas',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0277BD', // Medical Blue
                        '#00ACC1', // Cyan
                        '#00897B', // Teal
                        '#5E35B1', // Deep Purple
                        '#3949AB', // Indigo
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    public static function getDefaultColumnSpan(): int|string|array
    {
        return 1;
    }
} 