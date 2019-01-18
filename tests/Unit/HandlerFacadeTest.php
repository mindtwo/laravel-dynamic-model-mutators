<?php

namespace Mindtwo\DynamicMutators\Tests\Unit;

use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Facades\Handler;
use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;
use Mindtwo\DynamicMutators\Tests\TestCase;

class HandlerFacadeTest extends TestCase
{
    /**
     * Make with parameters in order test.
     *
     * @test
     */
    public function testMakeWithParametersInOrder()
    {
        $model = new MockModel();

        $handler = Handler::make([
            'name'        => 'example',
            'getMutator'  => ['exampleGet'],
            'setMutator'  => ['exampleSet'],
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Make with parameters in snake case test.
     *
     * @test
     */
    public function testMakeWithParametersInSnakeCase()
    {
        $model = new MockModel();

        $handler = Handler::make([
            'name'         => 'example',
            'get_mutator'  => ['exampleGet'],
            'set_mutator'  => ['exampleSet'],
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Make with parameters in different order test.
     *
     * @test
     */
    public function testMakeWithParametersDifferentOrder()
    {
        $model = new MockModel();

        $handler = Handler::make([
            'getMutator'  => ['exampleGet'],
            'setMutator'  => ['exampleSet'],
            'name'        => 'example',
        ])->setModel($model);

        $this->assertEquals('example', $handler->getName());
        $this->assertTrue($handler->hasGetMutator('property1'));
        $this->assertTrue($handler->hasSetMutator('property1'));
    }

    /**
     * Make throws an exception, if parameters are missing test.
     *
     * @test
     */
    public function testMakeThrowsAnExceptionIfParametersAreMissing()
    {
        $this->expectException(InvalidParameterException::class);

        Handler::make([
            'getMutator'  => ['exampleGet'],
            'setMutator'  => ['exampleSet'],
        ]);
    }
}
