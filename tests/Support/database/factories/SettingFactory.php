<?php

namespace OwowAgency\LaravelSettings\Tests\Support\Database\Factories;

use OwowAgency\LaravelSettings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;
use OwowAgency\LaravelSettings\Tests\Support\Models\User;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'model_id' => User::factory(),
            'model_type' => (new User)->getMorphClass(),
            'key' => HasSettings::getSettingsConfiguration()->keys()->random(),
            'value' => null,
        ];
    }
}