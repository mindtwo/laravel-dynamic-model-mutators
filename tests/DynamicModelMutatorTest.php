<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;
use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotCallableException;
use Mockery\Mock;

class DynamicModelMutatorTest extends TestCase
{
    /**
     * Mock eloquent model with dynamic model mutator trait.
     *
     * @return Model
     */
    protected function eloquentModelWithTrait()
    {
        return new class() extends Model {
            use DynamicModelMutator;

            protected $test = [
                'custom_property',
                'custom_fillable_property',
            ];

            protected $fillable = [
                'custom_fillable_property',
            ];

            protected $testValues = [
                'custom_property'          => 'TEST',
                'custom_fillable_property' => 'TEST',
            ];

            public function getAttributeTest(string $attribute): string
            {
                return $this->testValues[$attribute];
            }

            public function setAttributeTest(string $key, $value)
            {
                $this->testValues[$key] = $value;
            }
        };
    }

    public function testRegisterGetMutator()
    {
        $model = $this->eloquentModelWithTrait();

        $model::registerGetMutator('test', 'getAttributeTest');

        $this->assertEquals('TEST', $model->custom_property);
    }

    public function testRegisterSetMutator()
    {
        $model = $this->eloquentModelWithTrait();

        $model::registerSetMutator('test', 'setAttributeTest');

        $model->custom_property = 'Example';

        $this->assertEquals('Example', $model->custom_property);
    }

    public function testFillMutatedAttributes()
    {
        $model = $this->eloquentModelWithTrait();

        $model::registerSetMutator('test', 'setAttributeTest');

        $model->fill([
            'custom_fillable_property' => 'Example',
        ]);

        $this->assertEquals('Example', $model->getAttributeTest('custom_fillable_property'));
    }

    public function testAttributeIsNotFilledIfUndefined()
    {
        $model = $this->eloquentModelWithTrait();

        $model::registerSetMutator('test', 'setAttributeTest');

        $model->fill([
            'custom_property' => 'Example',
        ]);

        $this->assertEquals('TEST', $model->getAttributeTest('custom_property'));
    }

    public function testThrowExceptionWhenSetterMethodNameIsNotCallable()
    {
        $model = $this->eloquentModelWithTrait();

        $this->expectException(MutatorNotCallableException::class);

        $model::registerSetMutator('test', 'noneExistingMethod');
    }

    public function testThrowExceptionWhenGetterMethodNameIsNotCallable()
    {
        $model = $this->eloquentModelWithTrait();

        $this->expectException(MutatorNotCallableException::class);

        $model::registerGetMutator('test', 'noneExistingMethod');
    }
}
