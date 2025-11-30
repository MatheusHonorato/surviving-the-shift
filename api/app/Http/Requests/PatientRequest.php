<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'video_url' => 'required|string|max:255',
            'environments' => 'required|array|min:1',
            'environments.*' => 'required|exists:environments,id',
        ];
    }
}
