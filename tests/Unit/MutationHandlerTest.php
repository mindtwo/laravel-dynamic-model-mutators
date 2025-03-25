<?php

namespace Mindtwo\DynamicMutators\Tests\Unit;

use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotCallableException;
use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotDefinedException;
use Mindtwo\DynamicMutators\Handler\MutationHandler;
use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;
use Mindtwo\DynamicMutators\Tests\TestCase;

class MutationHandlerTest extends TestCase
{
    /**
     * Create mutation handler with get mutator test.
     *
     * @test
     */
    public function test_create_with_get_mutator()
    {
        $model = new MockModel;

        $handler = new MutationHandler('example', ['exampleGet']);
        $handler->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
    }

    /**
     * Create mutation handler with set mutator test.
     *
     * @test
     */
    public function test_create_with_set_mutator()
    {
        $model = new MockModel;

        $handler = new MutationHandler('example', [], ['exampleSet']);
        $handler->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Throws an exception, if get mutator is not defined test.
     *
     * @test
     */
    public function test_throws_an_exception_if_get_mutator_is_not_defined()
    {
        $this->expectException(DynamicMutatorNotDefinedException::class);

        $model = new MockModel;
        $handler = new MutationHandler('example', [], ['invalidGet']);
        $handler->setModel($model);

        $handler->callGetMutator('property1');
    }

    /**
     * Throws an exception, if set mutator is not defined test.
     *
     * @test
     */
    public function test_throws_an_exception_if_set_mutator_is_not_defined()
    {
        $this->expectException(DynamicMutatorNotDefinedException::class);

        $model = new MockModel;
        $handler = new MutationHandler('example', ['invalidSet']);
        $handler->setModel($model);

        $handler->callSetMutator('property1', 'value');
    }

    /**
     * Throws an exception, if get mutator is not callable test.
     *
     * @test
     */
    public function test_throws_an_exception_if_get_mutator_is_not_callable()
    {
        $this->expectException(DynamicMutatorNotCallableException::class);

        $model = new MockModel;
        $handler = new MutationHandler('example', ['invalidGet']);
        $handler->setModel($model);

        $handler->callGetMutator('property1');
    }

    /**
     * Throws an exception, if set mutator is not callable test.
     *
     * @test
     */
    public function test_throws_an_exception_if_set_mutator_is_not_callable()
    {
        $this->expectException(DynamicMutatorNotCallableException::class);

        $model = new MockModel;
        $handler = new MutationHandler('example', [], ['invalidSet']);
        $handler->setModel($model);

        $handler->callSetMutator('property1', 'value');
    }
}
