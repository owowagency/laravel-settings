<?php

return [

    /**
     * The table name for the settings table.
     */
    'table_name' => 'settings',

    /**
     * The JSON resources to be used in JSON responses.
     */
    'resources' => [
        'setting' => \OwowAgency\LaravelSettings\Http\Resources\SettingResource::class,
    ],

    'settings' => [

        /**
         * This is a configuration for an example setting. Here we can see all
         * the minimum required properties of a setting configuration.
         */
        'wants_promotion_emails' => [
            'title' => 'Receive commercial emails',
            'description' => 'Would you like to receive commercial emails for our marketing campaign?',
            'type' => 'bool',
            'default' => true,
            'nullable' => false,
        ],

        /**
         * This is a notification group. All notifications which are stored in
         * this group will be grouped.
         */
        'user_settings' => [

        ],

    ],

];
