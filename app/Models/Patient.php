<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User; // Asegúrate de importar User
use App\Models\Appointment; // Importa Appointment
use App\Models\ClinicalRecord; // Importa ClinicalRecord

class Patient extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * (Esta es la línea que corrige el error)
     */
    protected $fillable = [
        'user_id', // <-- ¡ESTA ES LA LÍNEA QUE FALTABA!
        'name',
        'carnet_identidad',
        'phone',
        'birthdate',
        'address',
    ];

    /**
     * RELACIÓN INVERSA: Un Paciente "pertenece a" un User.
     * (Necesaria para que 'user.email' funcione en la tabla)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIÓN: Un Paciente "tiene muchas" Citas.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * RELACIÓN: Un Paciente "tiene muchos" Historiales Clínicos.
     */
    public function clinicalRecords(): HasMany
    {
        return $this->hasMany(ClinicalRecord::class);
    }
}