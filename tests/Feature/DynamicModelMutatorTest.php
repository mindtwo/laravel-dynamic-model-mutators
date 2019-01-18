<?php

namespace Mindtwo\DynamicMutators\Tests\Feature;

use Mindtwo\DynamicMutators\Tests\TestCase;
use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;

class DynamicModelMutatorTest extends TestCase
{
    public function testDynamicMutators()
    {
        $model = new MockModel();

        $model->property1 = 'Property Test';
        $model->property2 = 'Property 2 Test';

        $this->assertEquals('Property Test', $model->property1);
        $this->assertEquals('Property 2 Test', $model->property2);
        $this->assertEquals('string-param', $model->setter_arguments['property1']);
        $this->assertEquals(['array', 'param'], $model->setter_arguments['property2']);
    }

    public function testDynamicMutatorsUseAppends()
    {
        $model = new MockModel();

        $model->property1 = 'Property Test';
        $model->property2 = 'Property 2 Test';

        $array = $model->toArray();

        $this->assertEquals('Property Test', $array['property1']);
        $this->assertEquals('Property 2 Test', $array['property2']);
    }
}
