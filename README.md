# Laravel Dynamic Model Mutators
[![Build Status](https://travis-ci.org/mindtwo/laravel-dynamic-model-mutators.svg?branch=master)](https://travis-ci.org/mindtwo/laravel-dynamic-model-mutators)
[![StyleCI](https://styleci.io/repos/159368194/shield)](https://styleci.io/repos/159368194)
[![Quality Score](https://img.shields.io/scrutinizer/g/mindtwo/laravel-dynamic-model-mutators.svg?style=flat-square)](https://scrutinizer-ci.com/g/mindtwo/laravel-dynamic-model-mutators)
[![Latest Stable Version](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/v/stable)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)
[![Total Downloads](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/downloads)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)
[![License](https://poser.pugx.org/mindtwo/laravel-dynamic-model-mutators/license)](https://packagist.org/packages/mindtwo/laravel-dynamic-model-mutators)

> Important Notice:
> The following documentation refers to packacke version 1.x
> We will add documentation for version 2.x as soon as possible. Sorry.


## Introduction
This package is an extension for Laravel's eloquent model. It allows you to define 
multiple get and set mutators by registering your own callback functions. This is 
a simple way to inject Laravel's getAttribute() and setAttribute() methods, 
especially within different traits on a single model.


## Installation

You can install the package via composer:

```bash
composer require mindtwo/laravel-dynamic-model-mutators
```

## How to use?

Use the "DynamicModelMutator" trait in your eloquent models:

```php
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
```php
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

registerSetMutator and registerGetMutator expecting two parameters. First is name 
of mutator, which is the name of the property which configures your attributes 
for the registered mutator, too. The 
second is the name of the callback function which will be called on the same object. 

#### Defining attributes for mutators
To define attributes for your mutators you have to set up a class property with
the mutator key as its name. For example we use 'translations' as mutator key, 
so we can setup our attributes by defining an $translations property on our model.

```php
namespace Examples;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;
use mindtwo\LaravelDynamicModelMutators\Interfaces\DynamicModelMutatorInterface; 

class exampleModel extends Model implements DynamicModelMutatorInterface 
{
    use use DynamicModelMutator;
    
    $translations = [
        'attribute_1',
        'attribute_2',
        'attribute_3'
    ];
}
```

Optionally you can set up a attribute based configuration which will be passed to 
you mutator methods. So you can add additional configuration to your attributes.

```php
namespace Examples;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;
use mindtwo\LaravelDynamicModelMutators\Interfaces\DynamicModelMutatorInterface; 

class exampleModel extends Model implements DynamicModelMutatorInterface 
{
    use use DynamicModelMutator;
    
    $translations = [
        'attribute_1' = 'string',
        'attribute_2' = 'text',
        'attribute_3' = [
            'type'    => string,
            'locales' => ['en', 'de'] 
        ]
    ];
}
```
   

#### Set mutator callback function
The callback function for a set mutators must accept three arguments. First is the
attribute name, second is the attribute value, third is the attribute specific
configuration. Because they will be called automatically within the trait, 
there is no need to return any value. 

```php
/**
 * Set attribute translation.
 *
 * @return void
 */
public function setAttributeTranslation($name, $value, $config=null)
{
    // Set value $value for attribute $name 
}
```   

#### Get mutator callback function
The callback function for a get mutators must accept two arguments. First is the
attribute name, second is the attribute specific configuration. It should return the
attribute value for a given attribute name. Note, that you can not use Laravel's
attribute casting feature for a dynamic mutated attribute, but it's quite easy to 
implement our own castings. 

```php
/**
 * Get attribute translation.
 *
 * @return void
 */
public function setAttributeTranslation($name, $config=null)
{
    // Get value for attribute $name 
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
 
