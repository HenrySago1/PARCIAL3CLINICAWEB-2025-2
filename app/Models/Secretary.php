<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Secretary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ci',
        'phone',
        // 'hire_date' si lo pusiste en la migración
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}