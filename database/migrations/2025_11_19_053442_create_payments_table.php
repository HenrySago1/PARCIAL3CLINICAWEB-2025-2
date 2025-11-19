<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Relaciones
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict'); // No borrar el servicio si ya se vendió
            
            // Datos del pago
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('efectivo'); // efectivo, qr, tarjeta
            $table->string('status')->default('pagado'); // pagado, pendiente
            $table->date('payment_date');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};