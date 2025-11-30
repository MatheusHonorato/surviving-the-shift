<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'type' => $this->type,
            'alternatives' => $this->alternatives
                ->shuffle()
                ->values()
                ->map(function ($alternative) {
                    return [
                        'id' => $alternative->id,
                        'description' => $alternative->description,
                    ];
                }),
        ];
    }
}
