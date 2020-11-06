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
        $this->setData($attribute, $value);

        if (is_null($this->value) && $this->canBeNull()) {
            return true;
        }

        $this->type = $this->getType();

        if ($this->type === null) {
            return false;
        }

        return $this->validate();
    }

    /**
     * Determine if the value of this rule can be nullable.
     *
     * @return bool
     */
    protected function canBeNull(): bool
    {
        $nullable = $this->getConfigValue('nullable', false);

        return SettingManager::convertToType('bool', $nullable);
    }

    /**
     * Execute the correct validation rule based on the given type.
     *
     * @return bool
     */
    private function validate(): bool
    {
        $method = sprintf(
            'validate%s',
            Str::ucfirst($this->getKeyword($this->type))
        );

        return $this->$method($this->attribute, $this->value);
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
        $key = $this->type === null
            ? 'in'
            : $this->getKeyword($this->type);

        return trans("validation.{$key}", [
            'attribute' => $this->attribute,
        ]);
    }
}
