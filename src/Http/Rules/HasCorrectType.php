<?php

namespace OwowAgency\LaravelSettings\Http\Rules;

use Illuminate\Support\Str;
use OwowAgency\LaravelSettings\Support\SettingManager;

class HasCorrectType extends BaseSettingRule
{
    /**
     * The type which is being validated by this rule.
     *
     * @var string
     */
    private $type;

    /**
     * Validates if value is a base64 string and the correct mime type.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        if (is_null($value) && $this->canBeNull($attribute)) {
            return true;
        }

        $this->type = $this->getType($attribute);

        if ($this->type === null) {
            return false;
        }

        return $this->validate($attribute, $value);
    }

    /**
     * Determine if the value of this rule can be nullable.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function canBeNull(string $attribute): bool
    {
        $nullable = $this->getConfigValue($attribute, 'nullable', false);

        return SettingManager::convertToType('bool', $nullable);
    }

    /**
     * Execute the correct validation rule based on the given type.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    private function validate(string $attribute, $value): bool
    {
        $method = sprintf(
            'validate%s',
            Str::ucfirst($this->getKeyword($this->type))
        );

        return $this->$method($attribute, $value);
    }

    /**
     * Get the keyword which is used by the validation system.
     *
     * @param  string  $type
     * @return string
     *
     * @throws \Exception
     */
    private function getKeyword(string $type): string
    {
        switch ($type) {
            case 'string':
            case 'array':
                return $type;
            case 'int':
            case 'double':
                return 'numeric';
            case 'bool':
                return 'boolean';
        }

        throw new \Exception(
            trans('laravel-settings::general.exceptions.unsupported_type', compact('type')),
        );
    }

    /**
     * Get the validation message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->type === null) {
            return trans('validation.in', [
                'attribute' => $this->configuration->keys(),
            ]);
        }

        $key = $this->getKeyword($this->type);

        return trans("validation.{$key}");
    }
}
