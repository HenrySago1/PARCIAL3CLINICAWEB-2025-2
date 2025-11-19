<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use App\Models\Appointment;
use Filament\Widgets\BarChartWidget;

class TopDoctorsChart extends BarChartWidget
{
    protected static ?string $heading = 'Doctores más solicitados';
    protected static ?int $sort = 2;

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
                    'backgroundColor' => '#3490dc',
                ],
            ],
            'labels' => $labels,
        ];
    }

    public static function getDefaultColumnSpan(): int|string|array
    {
        return 1;
    }
} 