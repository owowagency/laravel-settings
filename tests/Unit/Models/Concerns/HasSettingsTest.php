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

        return [$setting->model];
    }
}
