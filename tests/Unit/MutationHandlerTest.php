<?php

namespace Mindtwo\DynamicMutators\Tests\Unit;

use Mindtwo\DynamicMutators\Tests\TestCase;
use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;
use Mindtwo\DynamicMutators\Handler\MutationHandler;
use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotDefinedException;
use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotCallableException;

class MutationHandlerTest extends TestCase
{
    /**
     * Create mutation handler with get mutator test.
     *
     * @test
     */
    public function testCreateWithGetMutator()
    {
        $model = new MockModel();

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
    public function testCreateWithSetMutator()
    {
        $model = new MockModel();

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
    public function testThrowsAnExceptionIfGetMutatorIsNotDefined()
    {
        $this->expectException(DynamicMutatorNotDefinedException::class);

        $model = new MockModel();
        $handler = new MutationHandler('example', [], ['invalidGet']);
        $handler->setModel($model);

        $handler->callGetMutator('property1');
    }

    /**
     * Throws an exception, if set mutator is not defined test.
     *
     * @test
     */
    public function testThrowsAnExceptionIfSetMutatorIsNotDefined()
    {
        $this->expectException(DynamicMutatorNotDefinedException::class);

        $model = new MockModel();
        $handler = new MutationHandler('example', ['invalidSet']);
        $handler->setModel($model);

        $handler->callSetMutator('property1', 'value');
    }

    /**
     * Throws an exception, if get mutator is not callable test.
     *
     * @test
     */
    public function testThrowsAnExceptionIfGetMutatorIsNotCallable()
    {
        $this->expectException(DynamicMutatorNotCallableException::class);

        $model = new MockModel();
        $handler = new MutationHandler('example', ['invalidGet']);
        $handler->setModel($model);

        $handler->callGetMutator('property1');
    }

    /**
     * Throws an exception, if set mutator is not callable test.
     *
     * @test
     */
    public function testThrowsAnExceptionIfSetMutatorIsNotCallable()
    {
        $this->expectException(DynamicMutatorNotCallableException::class);

        $model = new MockModel();
        $handler = new MutationHandler('example', [], ['invalidSet']);
        $handler->setModel($model);

        $handler->callSetMutator('property1', 'value');
    }
}
