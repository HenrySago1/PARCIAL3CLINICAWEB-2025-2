<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_diagnoses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            
            // --- OJO DERECHO (OD) ---
            $table->string('image_path_right')->nullable();
            $table->string('result_right')->nullable();     // Ej: glaucoma, normal
            $table->float('probability_right')->nullable(); // Ej: 0.85

            // --- OJO IZQUIERDO (OI) ---
            $table->string('image_path_left')->nullable();
            $table->string('result_left')->nullable();
            $table->float('probability_left')->nullable();

            // Estado global del análisis
            $table->string('status')->default('pendiente'); // pendiente, procesando, completado, error
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_diagnoses');
    }
};