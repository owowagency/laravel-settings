<?php

namespace OwowAgency\LaravelSettings\Tests\Support\Concerns;

use Illuminate\Support\Collection;

trait HasSettings
{
    /**
     * Set up example settings and return them.
     *
     * @return array
     */
    public function setupSettings(): array
    {
        $configuration = static::getSettingsConfiguration()->toArray();

        config(['laravel-settings.settings' => $configuration]);

        return $configuration;
    }

    /**
     * Get the settings configuration which will be used.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getSettingsConfiguration(): Collection
    {
        return collect([
            'wants_promotion_emails' => [
                'title' => 'Receive commercial emails',
                'description' => 'Would you like to receive commercial emails for our marketing campaign?',
                'type' => 'bool',
                'default' => false,
                'nullable' => false,
            ],
            'delete_account' => [
                'title' => 'Delete my account after inactivity',
                'description' => 'If I don\'t login in for a certain amount of days, you may delete my account.',
                'type' => 'int',
                'default' => 365,
                'nullable' => true,
            ],
            'lang' => [
                'title' => 'What language do you speak?',
                'description' => 'We\'ll translate the application to your preferred language.',
                'type' => 'string',
                'default' => 'en',
                'nullable' => false,
            ],
        ]);
    }
}
