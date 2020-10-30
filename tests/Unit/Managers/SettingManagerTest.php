<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Managers;

use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Support\SettingManager;

class SettingManagerTest extends TestCase
{
    /** @test */
    public function it_returns_all_minimum_properties()
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
        ];

        $this->assertEquals($expected, $configured['key']);
    }

    /** @test */
    public function it_converts_to_booleans()
    {
        $converted = SettingManager::convertToType('boolean', 'true');

        $this->assertEquals(true, $converted);
    }

    /** @test */
    public function it_converts_to_strings()
    {
        $converted = SettingManager::convertToType('string', 123);

        $this->assertEquals('123', $converted);
    }

    /** @test */
    public function it_converts_to_int()
    {
        $converted = SettingManager::convertToType('int', '123');

        $this->assertEquals(123, $converted);
    }

    /** @test */
    public function it_doesnt_convert_null_objects()
    {
        $converted = SettingManager::convertToType('string', null);

        $this->assertNull($converted);
    }
}
