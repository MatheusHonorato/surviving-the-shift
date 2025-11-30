<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alternative extends Model
{
    use HasFactory;

    protected $fillable = ['step_id', 'description'];

    protected $casts = [
        'description' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
