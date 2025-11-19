<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;  // <-- Usa Spatie
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens; // <-- Importa HasOne


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'role' no es fillable, lo maneja Spatie
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- ¡AQUÍ ESTÁ LA FUNCIÓN QUE FALTA! ---
    /**
     * Relación con el paciente (si el usuario es paciente)
     * Esto es lo que busca el AuthController.
     */
    public function paciente(): HasOne
    {
        return $this->hasOne(Patient::class);
    }
    // ----------------------------------------

    /**
     * Relación con el doctor (si el usuario es doctor)
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function secretary(): HasOne
    {
        return $this->hasOne(Secretary::class);
    }

    // --- Métodos de Verificación de Rol (Están bien) ---
    public function isPaciente(): bool
    {
        return $this->hasRole('paciente');
    }
    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    public function isSecretary(): bool
    {
        return $this->hasRole('secretary'); // Asumo que 'secretary' es tu rol 'admin'
    }
}