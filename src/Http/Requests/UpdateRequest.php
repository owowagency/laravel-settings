<?php

namespace OwowAgency\LaravelSettings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OwowAgency\LaravelSettings\Support\SettingManager;
use OwowAgency\LaravelSettings\Http\Rules\HasCorrectType;
use OwowAgency\LaravelSettings\Http\Rules\SettingKeyExists;
use OwowAgency\LaravelSettings\Http\Rules\HasSettingGroupKey;

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
            'settings.*.key' => [
                'required',
                new SettingKeyExists,
            ],
            'settings.*.value' => [
                'present',
                new HasCorrectType($configuration),
            ],
            'settings.*.group' => [
                new HasSettingGroupKey($configuration),
            ],
        ];
    }
}
