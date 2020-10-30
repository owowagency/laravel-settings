<?php

namespace OwowAgency\LaravelSettings\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwowAgency\LaravelSettings\Support\SettingCollection;

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
     * @return \OwowAgency\LaravelSettings\Support\SettingCollection
     */
    public function getSettingsAttribute(): SettingCollection;
}
