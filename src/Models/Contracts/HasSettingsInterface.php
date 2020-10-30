<?php

namespace OwowAgency\LaravelSettings\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasSettingsInterface
{
    /**
     * Get all of the model's settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function settings(): MorphMany;
}
