<?php

namespace Mindtwo\DynamicMutators\Tests\Mocks;

use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Facades\Handler;
use Mindtwo\DynamicMutators\Model;

class MockModel extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The first mutator-driven property configuration.
     *
     * @var array<string, mixed>
     */
    public $example = [
        'property1' => 'string-param',
        'property2' => ['array', 'param'],
    ];

    /**
     * The second mutator-driven property configuration.
     *
     * @var array<string, mixed>
     */
    public $example_2 = [
        'property3' => 'string-param',
        'property4' => ['array', 'param'],
    ];

    /**
     * The in-memory store written to by the set mutator.
     *
     * @var array<string, mixed>
     */
    protected $values = [];

    /**
     * The arguments passed to the most recent set mutator invocation per attribute.
     *
     * @var array<string, mixed>
     */
    public $setter_arguments = [];

    /**
     * The arguments passed to the most recent get mutator invocation per attribute.
     *
     * @var array<string, mixed>
     */
    public $getter_arguments = [];

    /**
     * The accessors that should be appended to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'property1',
        'property2',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     *
     * @throws InvalidParameterException
     */
    public static function boot()
    {
        parent::boot();

        static::registerMutationHandler(Handler::make([
            'name' => 'example',
            'get_mutator' => ['exampleGet'],
            'set_mutator' => ['exampleSet'],
        ]));
    }

    /**
     * Persist the given value via the registered set mutator.
     *
     * @param  mixed  $value
     * @param  mixed  $arguments
     * @return $this
     */
    public function exampleSet(string $key, $value, $arguments = [])
    {
        $this->values[$key] = $value;
        $this->setter_arguments[$key] = $arguments;

        return $this;
    }

    /**
     * Resolve the given value via the registered get mutator.
     *
     * @param  mixed  $arguments
     * @return mixed|null
     */
    public function exampleGet(string $key, $arguments = [])
    {
        return $this->values[$key] ?? null;
    }
}
