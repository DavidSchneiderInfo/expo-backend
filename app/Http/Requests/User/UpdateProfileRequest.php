<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    private static string $interestedInSexRule = 'sometimes|bool';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()!==null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|between:1,20',
            'bio' => 'sometimes|string|between:1,500',
            'sex' => 'sometimes|in:f,m,x',
            'height' => 'sometimes|integer|between:60,240',
            'i_f' => self::$interestedInSexRule,
            'i_m' => self::$interestedInSexRule,
            'i_x' => self::$interestedInSexRule,
        ];
    }

    public function getAttributes(): array
    {
        return $this->only([
            'name',
            'bio',
            'sex',
            'height',
            'i_f',
            'i_m',
            'i_x',
        ]);
    }
}
