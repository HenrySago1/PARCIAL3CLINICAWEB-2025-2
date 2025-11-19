<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AiDiagnosis; // Importar el modelo
use App\Observers\AiDiagnosisObserver; // Importar el observer

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- ¡AQUÍ ESTÁ LA LÍNEA QUE FALTA! ---
        // Registramos el observer para que escuche los eventos del modelo AiDiagnosis
        AiDiagnosis::observe(AiDiagnosisObserver::class);
    }
}