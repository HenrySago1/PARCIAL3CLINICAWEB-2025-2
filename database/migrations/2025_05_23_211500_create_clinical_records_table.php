<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_records', function (Blueprint $table) {
            $table->id();
            
            // --- ENLACES ---
            // A qué paciente pertenece
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            
            // Qué doctor lo atendió
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            
            // A qué cita corresponde (¡NUEVO Y MUY IMPORTANTE!)
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');

            // --- DATOS MÉDICOS ---
            $table->text('diagnosis'); // Diagnóstico del doctor
            $table->text('treatment'); // Tratamiento
            $table->text('notes')->nullable(); // Notas adicionales
            
            // (Opcional) Enlace al diagnóstico de IA que mencionaste
            // $table->foreignId('ai_diagnosis_id')->nullable()->constrained('ai_diagnoses');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};