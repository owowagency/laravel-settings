<?php

namespace OwowAgency\LaravelSettings\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use OwowAgency\LaravelSettings\Support\SettingManager;
use Illuminate\Validation\Concerns\ValidatesAttributes;

abstract class BaseSettingRule implements Rule
{
    use ValidatesAttributes;

    /**
     * The attribute which we are validating the value of.
     *
     * @var string
     */
    protected $attribute;

    /**
     * The value which is being validated.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Set the validation data.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return void
     */
    protected function setData(string $attribute, $value): void
    {
        $this->attribute = $attribute;
        $this->value = $value;
    }

    /**
     * Get the type from the configuration based on the key which can be found
     * in the request.
     *
     * @return string
     */
    protected function getType(): ?string
    {
        return $this->getConfigValue('type');
    }

    /**
     * Get the value of a certain key from the configuration based on the key
     * which can be found in the request.
     *
     * @param  string  $typeKey
     * @param  mixed  $default
     * @return mixed
     */
    protected function getConfigValue(string $typeKey, $default = null)
    {
        $configKey = $this->buildConfigKey(
            request(str_replace(['value', 'group'], 'key', $this->attribute))
        );

        if (! SettingManager::exists($configKey)) {
            return $default;
        }

        return SettingManager::getConfiguredValue($configKey, $typeKey, $default);
    }

    /**
     * Build the configuration key so that it is always prepended with the group
     * key if needed.
     *
     * @param  string  $key
     * @return string
     */
    protected function buildConfigKey(string $key): string
    {
        if (! $this->belongsToGroup()) {
            return $key;
        }

        return sprintf('%s.%s', $this->getGroupKey(), $key);
    }

    /**
     * Determine if the attribute which needs to be validated is associated with
     * a group setting.
     *
     * @return bool
     */
    protected function belongsToGroup(): bool
    {
        return request()->has($this->getGroupAttributeName());
    }

    /**
     * Get the group key value from the request.
     *
     * @return string
     */
    protected function getGroupKey(): string
    {
        return request()->input($this->getGroupAttributeName());
    }

    /**
     * Get the attribute name for the group key.
     *
     * @return string
     */
    protected function getGroupAttributeName(): string
    {
        return str_replace(['value', 'key'], 'group', $this->attribute);
    }
}
