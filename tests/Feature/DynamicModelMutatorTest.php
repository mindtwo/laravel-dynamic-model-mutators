<?php

namespace Mindtwo\DynamicMutators\Tests\Feature;

use Mindtwo\DynamicMutators\Tests\Mocks\MockModel;
use Mindtwo\DynamicMutators\Tests\TestCase;

class DynamicModelMutatorTest extends TestCase
{
    /**
     * Assert that dynamic mutators round-trip values and forward configuration.
     *
     * @return void
     */
    public function test_dynamic_mutators()
    {
        $model = new MockModel;

        $model->property1 = 'Property Test';
        $model->property2 = 'Property 2 Test';

        $this->assertEquals('Property Test', $model->property1);
        $this->assertEquals('Property 2 Test', $model->property2);
        $this->assertEquals('string-param', $model->setter_arguments['property1']);
        $this->assertEquals(['array', 'param'], $model->setter_arguments['property2']);
    }

    /**
     * Assert that dynamically mutated attributes are included in toArray output via $appends.
     *
     * @return void
     */
    public function test_dynamic_mutators_use_appends()
    {
        $model = new MockModel;

        $model->property1 = 'Property Test';
        $model->property2 = 'Property 2 Test';

        $array = $model->toArray();

        $this->assertEquals('Property Test', $array['property1']);
        $this->assertEquals('Property 2 Test', $array['property2']);
    }
}
