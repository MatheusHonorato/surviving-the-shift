<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'step_id',
        'patient_id',
        'attempt',
        'alternative_id',
        'is_correct',
        'started_at',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'started_at' => 'datetime',
        'answered_at' => 'datetime',
    ];
}
