<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerKey extends Model
{
    use HasFactory;

    protected $fillable = ['step_id', 'alternative_id'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }
}
