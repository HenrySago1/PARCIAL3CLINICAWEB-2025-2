<?php

namespace App\Observers;

use App\Models\AiDiagnosis;
use App\Services\AiDiagnosisService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class AiDiagnosisObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the AiDiagnosis "created" event.
     */
    public function created(AiDiagnosis $aiDiagnosis): void
    {
        // Instanciar el servicio y llamar al método de análisis
        // Es recomendable usar Jobs para procesos largos, pero para empezar esto funciona
        $service = new AiDiagnosisService();
        $service->analyzeImage($aiDiagnosis);
    }

    /**
     * Handle the AiDiagnosis "updated" event.
     */
    public function updated(AiDiagnosis $aiDiagnosis): void
    {
        //
    }

    /**
     * Handle the AiDiagnosis "deleted" event.
     */
    public function deleted(AiDiagnosis $aiDiagnosis): void
    {
        //
    }

    /**
     * Handle the AiDiagnosis "restored" event.
     */
    public function restored(AiDiagnosis $aiDiagnosis): void
    {
        //
    }

    /**
     * Handle the AiDiagnosis "force deleted" event.
     */
    public function forceDeleted(AiDiagnosis $aiDiagnosis): void
    {
        //
    }
}