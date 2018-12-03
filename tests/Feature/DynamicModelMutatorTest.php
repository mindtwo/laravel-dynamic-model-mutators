<?php

namespace Tests\Feature;

use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotCallableException;
use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorOperatorNotDefinedException;
use Tests\Mocks\DynamicMutatorModel;
use Tests\TestCase;

class DynamicModelMutatorTest extends TestCase
{
    /**
     * Set and get mutated attributes test.
     *
     * @test
     */
    public function testSetAndGetMutatedAttributes()
    {
        $model = new DynamicMutatorModel();

        $model->attribute_1 = 'Example';
        $model->attribute_2 = 'Example';
        $model->attribute_3 = 'Example';

        $this->assertEquals('Example', $model->attribute_1);
        $this->assertEquals('example', $model->attribute_2);
        $this->assertEquals('EXAMPLE', $model->attribute_3);
    }

    /**
     * Fill mutated attributes test.
     *
     * @test
     */
    public function testFillAndGetMutatedAttribute()
    {
        $model = new DynamicMutatorModel([
            'attribute_1' => 'Example',
            'attribute_2' => 'Example',
            'attribute_3' => 'Example',
        ]);

        $this->assertEquals('Example', $model->attribute_1);
        $this->assertEquals('example', $model->attribute_2);
        $this->assertEquals('EXAMPLE', $model->attribute_3);
    }

    /**
     * Throws an exception, if getter method is not callable.
     *
     * @test
     */
    public function testThrowsAnExceptionIfGetterMethodIsNotCallable()
    {
        $model = new DynamicMutatorModel();

        $this->expectException(MutatorNotCallableException::class);

        $model::registerGetMutator('test', 'noneExistingMethod');
    }

    /**
     * Throws an exception, if setter method is not callable.
     *
     * @test
     */
    public function testThrowsAnExceptionIfSetterMethodIsNotCallable()
    {
        $model = new DynamicMutatorModel();

        $this->expectException(MutatorNotCallableException::class);

        $model::registerSetMutator('test', 'noneExistingMethod');
    }

    /**
     * Throws an exception, if mutator operator is invalid.
     *
     * @test
     */
    public function testThrowsAnExceptionIfMutatorOperatorIsInvalid()
    {
        $model = new DynamicMutatorModel();

        $this->expectException(MutatorOperatorNotDefinedException::class);

        $model::registerMutator('invalid', 'example_mutations', 'exampleGetMutator');
    }
}
