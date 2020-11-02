<?php

namespace OwowAgency\LaravelSettings\Models\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasSettingsInterface
{
    /**
     * Get all of the model's settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function settings(): MorphMany;

    /**
     * Get the all the setting values for the current model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSettings(): Collection;

    /**
     * Get the settings configuration by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getSettingConfig(string $key): ?array;

    /**
     * Get the settings value by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getSettingValue(string $key);

    /**
     * Get the unconverted settings value by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRawSettingValue(string $key);
}
