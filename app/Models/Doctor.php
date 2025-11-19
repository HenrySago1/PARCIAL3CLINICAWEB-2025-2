<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * (Asegúrate de que coincidan con tu migración de doctores)
     */
    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'morning_start',
        'morning_end',
        'afternoon_start',
        'afternoon_end',
        'vacation_days',
        // 'ci', 'phone', etc., si los tienes en la migración de doctores
    ];

    /**
     * RELACIÓN INVERSA: Un Doctor "pertenece a" un User.
     * Esto corrige el error "Undefined method 'user'".
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIÓN: Un Doctor "tiene muchas" Citas.
     * Esto es para que funcione el 'appointments_count' en la tabla.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}