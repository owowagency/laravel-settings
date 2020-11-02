# Laravel Settings

A package of settings features for the Laravel framework.

# Installation

Installing this package can be done easily via the following Artisan command.

```bash
composer require owowagency/laravel-settings
```

# Setup

To install all the vendor files you can run the following command.

```bash
php artisan vendor:publish --provider="OwowAgency\LaravelSettings\LaravelSettingsServiceProvider"
```

This will copy all the vendor files, including configuration, migrations and resources. If you wish to only install certain files you can use the command described in the next paragraphs. 

## Config

If  you wish to publish the configuration file, you can use the following command:

```bash
php artisan vendor:publish --provider="OwowAgency\LaravelSettings\LaravelSettingsServiceProvider" --tag=config
```

### Settings

The `settings` configuration holds all the values which can be used within the application. Each setting should have a unique key. For setting the key we would recommend using a package like [Laravel Enum](https://github.com/BenSampo/laravel-enum). Beside the key, we use the following attributes:

- `title` (default: `null`): here you can store a small title of the setting.
- `description` (default: `null`): a description about the setting which you might want to display to the user.
- `type` (default: `string`): the variable type of the setting (the type should be acceptable by the `[settype](https://www.php.net/manual/en/function.settype.php#refsect1-function.settype-description)` method).
- `default` (default: `null`): the default value which will be used if the user hasn't stored the value in the database yet.
- `nullable` (default: `true`): indicates if this setting may have the `null` value. 

### Table

The `table_name` configuration holds the value which is being used for the table name. By default, this has been set to `settings`, but if you wish to use a different table name you can change it with this configuration value.

### Resources

The package uses Laravel its [API Resources](https://laravel.com/docs/8.x/eloquent-resources#generating-resources). We ship the package with one resource, which will return the setting model. If you wish to use a custom resource, you can specify them in this list.

## Migrations

If  you wish to publish the migrations, you can use the following command:

```bash
php artisan vendor:publish --provider="OwowAgency\LaravelSettings\LaravelSettingsServiceProvider" --tag=migrations
```

## Routes

To set up all the routes needed for this package you can call the setting macro on the Route facade. By doing so, all the routes which are required by this package will be available to call. 

```php
Route::settings('users', App\Models\User::class);
```

After adding this to one of your routes files (i.e. `routes/api.php`), the following two routes will be available.

### Index

This route can be used to index all the settings of a user: `GET /users/{user}/settings`. This route will always return all configured settings in the `settings` config value. Also, values which are not yet stored for the authenticated user. The package will then use the default configured value.

**Response**

```json
[
    {
        "title": "Receive commercial emails",
        "description": "Would you like to receive commercial emails for our marketing campaign?",
        "type": "bool",
        "default": false,
        "nullable": false,
        "key": "wants_promotion_emails",
        "value": false
    }
]
```

### Update

This route can be used to update the given setting values: `PATCH /users/{user}/settings`.

**Request**

```json
[
    {
        "key": "wants_promotion_emails",
        "value": true
    } 
]
```

**Response**

```json
[
    {
        "title": "Receive commercial emails",
        "description": "Would you like to receive commercial emails for our marketing campaign?",
        "type": "bool",
        "default": false,
        "nullable": false,
        "key": "wants_promotion_emails",
        "value": true
    }
]
```

# Usage

If you want a certain model (we'll use the user model in this example) to have settings you can add the `OwowAgency\LaravelSettings\Models\Concerns\HasSettings` trait to the model. First you should add the `OwowAgency\LaravelSettings\Models\Concerns\HasSettingsInterface` to the model. Your model could look like this.

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use OwowAgency\LaravelSettings\Models\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class User extends BaseUser implements HasSettingsInterface
{
    use HasSettings;
}
```

Now, to get a certain config value from the user you can do this:

```php
$user->settings->getValue('wants_promotion_emails');

// Or

$user->settings->wants_promotion_emails;
```