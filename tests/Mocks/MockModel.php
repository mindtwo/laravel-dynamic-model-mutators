<?php

namespace Mindtwo\DynamicMutators\Tests\Mocks;

use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Facades\Handler;
use Mindtwo\DynamicMutators\Model;

class MockModel extends Model
{
    protected $guarded = [];

    public $example = [
        'property1' => 'string-param',
        'property2' => ['array', 'param'],
    ];

    public $example_2 = [
        'property3' => 'string-param',
        'property4' => ['array', 'param'],
    ];

    protected $values = [];
    public $setter_arguments = [];
    public $getter_arguments = [];

    protected $appends = [
        'property1',
        'property2',
    ];

    /**
     * Example model boot method.
     *
     * @throws InvalidParameterException
     */
    public static function boot()
    {
        parent::boot();

        static::registerMutationHandler(Handler::make([
            'name'        => 'example',
            'get_mutator' => ['exampleGet'],
            'set_mutator' => ['exampleSet'],
        ]));
    }

    /**
     * Example set mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $arguments
     * @return $this
     */
    public function exampleSet(string $key, $value, $arguments = [])
    {
        $this->values[$key] = $value;
        $this->setter_arguments[$key] = $arguments;

        return $this;
    }

    /**
     * Example get mutator.
     *
     * @param  string  $key
     * @param  array  $arguments
     * @return mixed|null
     */
    public function exampleGet(string $key, $arguments = [])
    {
        return $this->values[$key] ?? null;
    }
}
