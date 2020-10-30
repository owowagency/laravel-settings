<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Models;

use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Support\SettingCollection;
use OwowAgency\LaravelSettings\Support\SettingManager;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class SettingTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_converts_to_correct_type(): void
    {
        $setting = Setting::factory()->create([
            'key' => 'wants_promotion_emails',
            'value' => true,
        ]);

        // Refresh the model so that the value attribute becomes a string
        // instead of a boolean.
        $setting->refresh();

        $this->assertTrue($setting->converted_value);
    }

    /** @test */
    public function it_doesnt_store_config_values(): void
    {
        $setting = Setting::factory()->create([
            'key' => 'lang',
            'value' => 'nl',
        ]);

        $filled = SettingManager::fillWithSettingsConfig([$setting])->first();

        $this->assertEquals(
            data_get($this->getSettingsConfiguration(), 'lang.title'),
            $filled->title
        );

        $filled->save();

        // The (for example) title key should have been reset at this point.
        $this->assertNull($filled->title);
    }

    /** @test */
    public function it_returns_a_setting_collection(): void
    {
        $setting = Setting::factory()->create();

        $settings = $setting->model->settings;

        $this->assertInstanceOf(SettingCollection::class, $settings);
    }
}