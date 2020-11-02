<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Models\Concerns;

use Illuminate\Support\Collection;
use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class HasSettingsTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_can_get_settings()
    {
        [$user] = $this->prepare();

        $settings = $user->getSettings();

        $this->assertInstanceOf(Collection::class, $settings);
        $this->assertJsonStructureSnapshot($settings);
    }

    /** @test */
    public function it_can_get_a_specific_setting_configurations()
    {
        [$user] = $this->prepare();

        $settings = $user->getSettingConfig('lang');

        $this->assertIsArray($settings);
        $this->assertJsonStructureSnapshot($settings);
    }

    /** @test */
    public function it_can_get_a_specific_setting_value()
    {
        [$user] = $this->prepare();

        $this->assertEquals('nl', $user->getSettingValue('lang'));
    }

    /** @test */
    public function it_can_get_a_specific_setting_raw_value()
    {
        [$user] = $this->prepare($key = 'delete_account', $value = '365');

        $this->assertSame($value, $user->getRawSettingValue($key));
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
