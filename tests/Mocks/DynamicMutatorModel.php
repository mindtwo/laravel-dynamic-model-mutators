<?php

namespace mindtwo\LaravelDynamicModelMutators\Tests\Mocks;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;

class DynamicMutatorModel extends Model
{
    use DynamicModelMutator;

    protected $guarded = [];

    protected $example_mutations = [
        'attribute_1',
        'attribute_2' => 'to_lower',
        'attribute_3' => ['to_upper' => true],
    ];

    protected $example_values = [];

    public static function boot()
    {
        parent::boot();
        static::registerSetMutator('example_mutations', 'exampleSetMutator');
        static::registerGetMutator('example_mutations', 'exampleGetMutator');
    }

    public function exampleGetMutator($attributeName, $configuration = null)
    {
        return $this->example_values[$attributeName] ?? null;
    }

    public function exampleSetMutator($attributeName, $value, $configuration = null)
    {
        if ('to_lower' == $configuration) {
            $value = strtolower($value);
        } elseif (isset($configuration['to_upper'])) {
            $value = strtoupper($value);
        }

        $this->example_values[$attributeName] = $value;
    }
}
