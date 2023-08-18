<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Managers;

use Illuminate\Support\Arr;
use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Support\SettingManager;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class SettingManagerTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_returns_all_minimum_properties(): void
    {
        config(['laravel-settings.settings' => [
            'key' => [
                'type' => 'int'
            ],
        ]]);

        $configured = SettingManager::getConfigured();

        $expected = [
            'title' => null,
            'description' => null,
            'type' => 'int',
            'default' => null,
            'nullable' => true,
        ];

        $this->assertEquals($expected, $configured['key']);
    }

    /** @test */
    public function it_doesnt_add_minimum_properties_to_groups()
    {
        $configured = SettingManager::getConfigured();

        $keys = [
            'app_settings.title',
            'app_settings.description',
            'app_settings.type',
            'app_settings.default',
            'app_settings.nullable',
        ];

        $this->assertFalse(Arr::has($configured, $keys));
    }

    /** @test */
    public function it_converts_to_booleans(): void
    {
        $converted = SettingManager::convertToType('boolean', 'true');

        $this->assertEquals(true, $converted);
    }

    /** @test */
    public function it_converts_to_strings(): void
    {
        $converted = SettingManager::convertToType('string', 123);

        $this->assertEquals('123', $converted);
    }

    /** @test */
    public function it_converts_to_int(): void
    {
        $converted = SettingManager::convertToType('int', '123');

        $this->assertEquals(123, $converted);
    }

    /** @test */
    public function it_doesnt_convert_null_objects(): void
    {
        $converted = SettingManager::convertToType('string', null);

        $this->assertNull($converted);
    }

    /** @test */
    public function it_determines_if_settings_exists(): void
    {
        $this->assertTrue(SettingManager::exists('lang'));
    }

    /** @test */
    public function it_determines_if_settings_exists_in_groups(): void
    {
        $this->assertTrue(SettingManager::exists('app_settings.dark_mode'));
    }

    /** @test */
    public function it_determines_if_settings_dont_exists(): void
    {
        $this->assertFalse(SettingManager::exists('lang.title'));
    }

    /** @test */
    public function it_fills_settings_with_configuration_values(): void
    {
        $setting = Setting::factory()->create([
            'key' => 'lang',
            'value' => 'nl',
        ]);

        $filled = SettingManager::fillWithSettingsConfig([$setting]);

        $this->assertEquals('en', $filled->first()->default);
    }

    /** @test */
    public function it_fills_unknown_settings(): void
    {
        $setting = Setting::factory()->create([
            'key' => '💩',
            'value' => 'What!?',
        ]);

        $filled = SettingManager::fillWithSettingsConfig([$setting])->first();

        $keys = [
            'title' => null,
            'description' => null,
            'type' => 'string',
            'default' => null,
            'nullable' => true,
        ];

        foreach ($keys as $key => $value) {
            $this->assertEquals($value, $filled->$key);
        }
    }
}
