# Laravel Address Prefiller

![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Nova Translatable

[![Version](https://img.shields.io/packagist/v/marshmallow/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller)
[![Downloads](https://shields.io/packagist/dt/marshmallow/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller)
[![Issues](https://img.shields.io/github/issues/marshmallow-packages/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller)
[![Licence](https://img.shields.io/github/license/marshmallow-packages/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller)

This package will prefill address field based on a provided zipcode and housenumber. This currently only supports Dutch addresses. This can be used in your custom applications or you can use it with Laravel Nova.

## Installation

### Installing the package via Composer

Install the package in a Laravel project via Composer.

```bash
# Install the package
composer require marshmallow/address-prefiller
```

### Usage in Nova

There is a custom Laravel Nova field available which you can use. This works very simular to the Laravel Place field. The difference is that this field will not talk to Algolia but to the official open api provided by the dutch goverment which should contain all the latest information.

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),
        $this->addressFields(),
    ];
}

protected function addressFields()
{
    return $this->merge([

        /**
         * @param [string] $name The name of the zipcode field
         * @param [string] $attribute The column name of the zipcode field
         * @param [string] $name_2 The name of your housenumber field
         * @param [string] $attribute_2 The column name of the house number field
         */
        Zipcode::make(__('Zipcode'), 'column_1', __('Housenumber'), 'column_11')

            /**
             * Let the package know which columns are connected to
             * the fields. The default values are commented after each
             * function call. Is your column names match these defaults,
             * you don't need to call all these functions.
             */
            ->street('column_2')        // street
            ->city('column_3')          // city
            ->province('column_4')      // province
            ->country('column_7')       // country
            ->latitude('column_8')      // latitude
            ->longitude('column_9'),    // longitude

        /**
         * The field below will all be prefilled with the collected
         * data if we find a match on the submitted zipcode and housenumber.
         */
        Text::make('Street', 'column_2')->hideFromIndex(),
        Text::make('City', 'column_3')->hideFromIndex(),
        Text::make('Province', 'column_4')->hideFromIndex(),
        Country::make('Country', 'column_7')->hideFromIndex(),
        Text::make('Latitude', 'column_8')->hideFromIndex(),
        Text::make('Longitude', 'column_9')->hideFromIndex(),
    ]);
}
```

### Usage manualy

We have provided an example on how you can use this functionality in your own application. We've currently only used this in the Nova setting so if you're missing anything, please let us know!

```php

use Marshmallow\Zipcode\Facades\Zipcode;

return Zipcode::get(
    $request->zipcode,
    $request->housenumber
);
```

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Credits

-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
