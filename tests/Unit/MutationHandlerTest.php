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
     * Assert that a handler can be created with only a get mutator.
     *
     * @test
     *
     * @return void
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
     * Assert that a handler can be created with only a set mutator.
     *
     * @test
     *
     * @return void
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
     * Assert that calling an undefined get mutator throws.
     *
     * @test
     *
     * @return void
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
     * Assert that calling an undefined set mutator throws.
     *
     * @test
     *
     * @return void
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
     * Assert that a get mutator pointing to a non-callable target throws.
     *
     * @test
     *
     * @return void
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
     * Assert that a set mutator pointing to a non-callable target throws.
     *
     * @test
     *
     * @return void
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
