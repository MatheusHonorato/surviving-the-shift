<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'step_id' => 'required|exists:steps,id',
            'alternative_id' => 'required|exists:alternatives,id',
        ];
    }
}
