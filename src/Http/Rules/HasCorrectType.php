<?php

namespace OwowAgency\LaravelSettings\Http\Rules;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;
use OwowAgency\LaravelSettings\Support\SettingManager;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class HasCorrectType implements Rule
{
    use ValidatesAttributes;

    /**
     * The collection containing all configuration values.
     *
     * @var \Illuminate\Support\Collection
     */
    private $configuration;

    /**
     * The type which is being validated by this rule.
     *
     * @var string
     */
    private $type;

    /**
     * HasCorrectType constructor.
     *
     * @param  \Illuminate\Support\Collection|null  $configuration
     */
    public function __construct(?Collection $configuration = null)
    {
        $this->configuration = $configuration ?? SettingManager::getConfigured();
    }

    /**
     * Validates if value is a base64 string and the correct mime type.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        $this->type = $this->getType($attribute);

        if ($this->type === null) {
            return false;
        }

        return $this->validate($attribute, $value);
    }

    /**
     * Get the type from the configuration based on the key which can be found
     * in the request.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getType(string $attribute): string
    {
        $key = str_replace('value', 'key', $attribute);

        return data_get($this->configuration[request($key)], 'type');
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
            trans('laravel-settings::general.exceptions.unsupported_type', [
                'type' => $this->type,
            ]),
        );
    }

    /**
     * Get the validation message.
     *
     * @return string
     */
    public function message()
    {
        $key = $this->getKeyword($this->type);

        return trans("validation.{$key}");
    }
}