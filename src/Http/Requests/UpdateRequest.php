<?php

namespace OwowAgency\LaravelSettings\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use OwowAgency\LaravelSettings\Support\SettingManager;
use OwowAgency\LaravelSettings\Http\Rules\HasCorrectType;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $configuration = SettingManager::getConfigured();

        return [
            'settings' => [
                'required',
                'array',
            ],
            'settings.*.key' => [
                'required',
                Rule::in($configuration->keys()),
            ],
            'settings.*.value' => [
                'required',
                new HasCorrectType($configuration),
            ],
        ];
    }
}
