<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Models\Concerns;

use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Support\SettingCollection;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class HasSettingsTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_can_get_settings(): void
    {
        [$user] = $this->prepare();

        $settings = $user->settings;

        $this->assertInstanceOf(SettingCollection::class, $settings);

        // Note that the settings are not converted to their associated type.
        // This happens in the resource. So a boolean value is now still the raw
        // value for dark_mode is now still "1", which is correct.
        $this->assertJsonStructureSnapshot($settings);
    }

    /**
     * Prepares for tests.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return array
     */
    private function prepare(string $key = 'lang', $value = 'nl'): array
    {
        $setting = Setting::factory()->create([
            'key' => $key,
            'value' => $value,
        ]);

        Setting::factory()->create([
            'group' => 'app_settings',
            'key' => 'dark_mode',
            'value' => true,
            'model_id' => $setting->model_id
        ]);

        return [$setting->model];
    }
}
