<?php

namespace OwowAgency\LaravelSettings\Models\Concerns;

use OwowAgency\LaravelSettings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwowAgency\LaravelSettings\Support\SettingManager;
use OwowAgency\LaravelSettings\Support\SettingCollection;

trait HasSettings
{
    /**
     * Get all of the model's settings.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'model');
    }

    /**
     * Get the all the setting values for the current model.
     *
     * @return \OwowAgency\LaravelSettings\Support\SettingCollection
     */
    public function getSettingsAttribute(): SettingCollection
    {
        return SettingManager::getForModel($this);
    }
}
