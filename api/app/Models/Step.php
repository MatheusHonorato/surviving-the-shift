<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'type'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class);
    }

    public function answerKeys(): HasMany
    {
        return $this->hasMany(AnswerKey::class);
    }
}
