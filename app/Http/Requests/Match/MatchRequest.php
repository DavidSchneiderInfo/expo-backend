<?php

namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class MatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    public function getLatitude(): float
    {
        return (float)$this->get('latitude');
    }

    public function getLongitude(): float
    {
        return (float)$this->get('longitude');
    }
}
