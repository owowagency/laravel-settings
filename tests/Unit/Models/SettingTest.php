<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Models;

use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class SettingTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_converts_to_correct_type()
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
}