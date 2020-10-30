<?php

namespace OwowAgency\LaravelSettings\Models\Concerns;

use OwowAgency\LaravelSettings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
}
