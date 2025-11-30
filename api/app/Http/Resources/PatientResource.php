<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'video_url' => $this->video_url,
            'length' => $this->length,
            'environments' => $this->environments->map(function ($environment) {
                return [
                    'id' => $environment->id,
                    'name' => $environment->name,
                    'value' => $environment->value,
                ];
            }),
            'steps' => $this->steps->map(function ($step) {
                return [
                    'id' => $step->id,
                ];
            }),
        ];
    }
}
