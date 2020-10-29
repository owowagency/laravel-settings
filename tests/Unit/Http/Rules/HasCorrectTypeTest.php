<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Http\Rules;

use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Http\Rules\HasCorrectType;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;

class HasCorrectTypeTest extends TestCase
{
    use HasSettings;

    /** @test */
    public function it_validates_booleans(): void
    {
        $rule = $this->mockRule('bool');

        $this->assertTrue($rule->passes('settings.0.value', true));
    }

    /** @test */
    public function it_fails_for_invalid_booleans(): void
    {
        $rule = $this->mockRule('bool');

        $this->assertFalse($rule->passes('settings.0.value', 'true'));
    }

    /** @test */
    public function it_validates_strings(): void
    {
        $rule = $this->mockRule('string');

        $this->assertTrue($rule->passes('settings.0.value', 'string'));
    }

    /** @test */
    public function it_fails_for_invalid_strings(): void
    {
        $rule = $this->mockRule('string');

        $this->assertFalse($rule->passes('settings.0.value', 1));
    }

    /** @test */
    public function it_validates_integers(): void
    {
        $rule = $this->mockRule('int');

        $this->assertTrue($rule->passes('settings.0.value', 1));
    }

    /** @test */
    public function it_fails_for_invalid_integers(): void
    {
        $rule = $this->mockRule('int');

        $this->assertFalse($rule->passes('settings.0.value', 'string'));
    }

    /** @test */
    public function it_validates_arrays(): void
    {
        $rule = $this->mockRule('array');

        $this->assertTrue($rule->passes('settings.0.value', []));
    }

    /** @test */
    public function it_fails_for_invalid_arrays(): void
    {
        $rule = $this->mockRule('array');

        $this->assertFalse($rule->passes('settings.0.value', 'string'));
    }

    /**
     * Mock the rule.
     *
     * @param  string  $type
     * @return \OwowAgency\LaravelSettings\Http\Rules\HasCorrectType
     */
    private function mockRule(string $type): HasCorrectType
    {
        return $this->mock(HasCorrectType::class, function ($mock) use ($type) {
            $mock->shouldAllowMockingProtectedMethods()
                ->makePartial()
                ->shouldReceive('getType')
                ->once()
                ->andReturn($type);
        });
    }
}