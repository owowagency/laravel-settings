<?php

namespace OwowAgency\LaravelSettings\Http\Rules;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;
use OwowAgency\LaravelSettings\Support\SettingManager;
use Illuminate\Validation\Concerns\ValidatesAttributes;

abstract class BaseSettingRule implements Rule
{
    use ValidatesAttributes;

    /**
     * The collection containing all configuration values.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $configuration;

    /**
     * BaseSettingRule constructor.
     *
     * @param  \Illuminate\Support\Collection|null  $configuration
     */
    public function __construct(?Collection $configuration = null)
    {
        $this->configuration = $configuration ?? SettingManager::getConfigured();
    }

    /**
     * Get the type from the configuration based on the key which can be found
     * in the request.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getType(string $attribute): ?string
    {
        return $this->getConfigValue($attribute, 'type');
    }

    /**
     * Get the value of a certain key from the configuration based on the key
     * which can be found in the request.
     *
     * @param  string  $attribute
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function getConfigValue(string $attribute, string $key, $default = null)
    {
        $configKey = request(str_replace('value', 'key', $attribute));

        if (! $this->configuration->offsetExists($configKey)) {
            return $default;
        }

        return data_get($this->configuration[$configKey], $key, $default);
    }
}
