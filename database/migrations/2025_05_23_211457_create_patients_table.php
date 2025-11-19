<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crea la tabla 'patients' con la estructura correcta
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            
            // --- Relación con el usuario de Laravel (Credenciales de la App Móvil) ---
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // --- Datos Personales del Paciente ---
            // (Usando los nombres de tu último PatientResource)
            $table->string('name'); // Nombre Completo
            $table->string('carnet_identidad')->unique();
            $table->string('phone')->nullable();
            $table->date('birthdate')->nullable();
            $table->text('address')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
};