<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelDynamicModelMutators\DynamicModelMutator;
use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotCallableException;

class DynamicModelMutatorTest extends TestCase
{
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

    protected function eloquentModelWithTrait()
    {
        return new class() extends Model {
            use DynamicModelMutator;

            protected $test = ['custom_property'];
            protected $testValues = ['custom_property' => 'TEST'];

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
}
