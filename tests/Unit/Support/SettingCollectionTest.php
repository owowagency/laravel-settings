<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Models\Concerns;

use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class SettingCollectionTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_can_get_a_specific_setting_configurations(): void
    {
        [$user] = $this->prepare();

        $settings = $user->settings->getConfig('lang');

        $this->assertIsArray($settings);
        $this->assertJsonStructureSnapshot($settings);
    }

    /** @test */
    public function it_can_get_a_specific_setting_value(): void
    {
        [$user] = $this->prepare();

        $this->assertEquals('nl', $user->settings->getValue('lang'));
    }

    /** @test */
    public function it_can_get_a_specific_setting_value_via_a_helper_method(): void
    {
        [$user] = $this->prepare();

        $this->assertEquals('nl', $user->settings->lang);
    }

    /** @test */
    public function it_can_get_a_specific_setting_raw_value(): void
    {
        [$user] = $this->prepare($key = 'delete_account', $value = '365');

        $this->assertSame($value, $user->settings->getRawValue($key));
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
