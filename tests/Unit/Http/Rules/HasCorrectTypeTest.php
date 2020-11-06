<?php

namespace OwowAgency\LaravelSettings\Tests\Unit\Http\Rules;

use Mockery\MockInterface;
use OwowAgency\LaravelSettings\Support\SettingManager;
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

    /** @test */
    public function it_validates_nullable_config_values(): void
    {
        $rule = $this->mockRule('string', true);

        $this->assertTrue($rule->passes('settings.0.value', null));
    }

    /** @test */
    public function it_throws_an_exception_on_incorrect_type(): void
    {
        // Contains a typo.
        $rule = $this->mockRule('strng');

        $this->expectException(\Exception::class);

        $rule->passes('settings.0.value', 'ERROR 💩!');
    }

    /** @test */
    public function it_returns_a_valid_error_message(): void
    {
        $rule = $this->mockRule('string');

        $rule->passes($attribute = 'settings.0.value', 1);

        $message = trans('validation.string', compact('attribute'));

        $this->assertEquals($message, $rule->message());
    }

    /**
     * Mock the rule.
     *
     * @param  string  $type
     * @param  bool  $allowNullable
     * @return \OwowAgency\LaravelSettings\Http\Rules\HasCorrectType
     */
    private function mockRule(string $type, $allowNullable = false): HasCorrectType
    {
        $mock = \Mockery::mock(HasCorrectType::class, [SettingManager::getConfigured()])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mock->shouldReceive('getType')
            ->andReturn($type);

        if ($allowNullable) {
            $mock->shouldReceive('canBeNull')
                ->once()
                ->andReturn(true);
        }

        $this->instance(HasCorrectType::class, $mock);

        return app(HasCorrectType::class);
    }
}
