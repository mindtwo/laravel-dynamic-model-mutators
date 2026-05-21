<?php

namespace Mindtwo\DynamicMutators\Tests\Unit;

use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Facades\Handler;
use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;
use Mindtwo\DynamicMutators\Tests\TestCase;

class HandlerFacadeTest extends TestCase
{
    /**
     * Assert that make() accepts constructor arguments in their natural order.
     *
     * @test
     *
     * @return void
     */
    public function test_make_with_parameters_in_order()
    {
        $model = new MockModel;

        $handler = Handler::make([
            'name' => 'example',
            'getMutator' => ['exampleGet'],
            'setMutator' => ['exampleSet'],
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Assert that make() accepts snake_case aliases for camelCase parameters.
     *
     * @test
     *
     * @return void
     */
    public function test_make_with_parameters_in_snake_case()
    {
        $model = new MockModel;

        $handler = Handler::make([
            'name' => 'example',
            'get_mutator' => ['exampleGet'],
            'set_mutator' => ['exampleSet'],
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Assert that make() resolves arguments by name regardless of order.
     *
     * @test
     *
     * @return void
     */
    public function test_make_with_parameters_different_order()
    {
        $model = new MockModel;

        $handler = Handler::make([
            'getMutator' => ['exampleGet'],
            'setMutator' => ['exampleSet'],
            'name' => 'example',
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Assert that make() throws when a required constructor argument is missing.
     *
     * @test
     *
     * @return void
     */
    public function test_make_throws_an_exception_if_parameters_are_missing()
    {
        $this->expectException(InvalidParameterException::class);

        Handler::make([
            'getMutator' => ['exampleGet'],
            'setMutator' => ['exampleSet'],
        ]);
    }
}
