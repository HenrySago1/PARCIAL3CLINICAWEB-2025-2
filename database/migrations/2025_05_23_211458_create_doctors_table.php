<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            
            // --- Relación con el usuario de Laravel (Login Web) ---
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // --- Datos Personales (de tu formulario) ---
            $table->string('name'); // 'name' en tu formulario
            $table->string('specialty')->default('Oftalmología');
            
            // Horarios
            $table->time('morning_start')->nullable();
            $table->time('morning_end')->nullable();
            $table->time('afternoon_start')->nullable();
            $table->time('afternoon_end')->nullable();
            
            // Vacaciones (Spatie Media Library es mejor para 'multiple')
            $table->date('vacation_days')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};