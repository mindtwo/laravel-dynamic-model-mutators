# Laravel Dynamic Model Mutators
[![Latest Stable Version](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/v/stable)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)
[![Total Downloads](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/downloads)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)
[![License](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/license)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)

## Installation

You can install the package via composer:

```bash
composer require mindtwo/laravel-dynamic-model-mutators
```

## How to use?

Use the "DynamicModelMutator" trait in your eloquent models:

```bash
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;

class User extends Model
{
    use DynamicModelMutator;
}
```

In the boot method of the model you can now register the dynamic getter and setter functions e.g.:
```
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::registerSetMutator('translations', 'setAttributeTranslation');
        static::registerGetMutator('translations', 'getAttributeTranslation');
    }
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@mindtwo.de instead of using the issue tracker.

## Credits

- [mindtwo GmbH](https://github.com/mindtwo)
- [All Other Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
 
