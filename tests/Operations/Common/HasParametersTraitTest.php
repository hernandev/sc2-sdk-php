<?php

namespace SteemConnect\Operations\Common;

use SteemConnect\TestCase;

/**
 * Class HasParametersTraitTest.
 *
 * Tests for the HasParameters trait.
 */
class HasParametersTraitTest extends TestCase
{
    // import the trait itself on the test class.
    use HasParameters;

    /**
     * Tests for empty start and basic single setter and getter.
     */
    public function test_parameters_setter_and_getter()
    {
        // assert the number of parameters starts as zero.
        $this->assertEquals(0, count($this->getParameters()));

        // assert the parameter does not previously exists.
        // meaning, it must return null.
        $this->assertNull($this->getParameter('foo'));

        // set a given parameter
        $setReturn = $this->setParameter('foo', 'bar');

        // assert the fluent return.
        $this->assertSame($this, $setReturn);

        // assert the parameter was correctly set.
        $this->assertEquals('bar', $this->getParameter('foo'));
    }

    /**
     * Test batch setters and getters of parameters.
     */
    public function test_batch_parameter_setter_and_getter()
    {
        // assert the number of parameters starts as zero.
        $this->assertEquals(0, count($this->getParameters()));

        // set a given number of parameters (2).
        $setReturn = $this->setParameters([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        // assert the fluent return.
        $this->assertSame($this, $setReturn);

        // assert that now there are two parameters.
        $this->assertEquals(2, count($this->getParameters()));

        // assert the parameters are the ones previously set.
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->getParameters());
    }

    /**
     * Test batch parameter setter, with the merge option.
     */
    public function test_batch_parameter_merge()
    {
        // set an initial parameter
        $this->setParameter('foo', 'bar');

        // set additional parameters, this time merging.
        $this->setParameters(['bar' => 'baz'], true);

        // assert the parameters are the ones previously set.
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->getParameters());
    }

    /**
     * Test for forgetting a given parameter by key.
     */
    public function test_parameter_forget()
    {
        // set a given parameter.
        $this->setParameter('foo', 'bar');

        // assert it was set on the first place.
        $this->assertEquals('bar', $this->getParameter('foo'));

        // forget the parameter previously set.
        $forgetReturn = $this->forgetParameter('foo');

        // since the parameter was removed, true should be returned.
        $this->assertTrue($forgetReturn);

        // after removal, the return should be now null.
        $this->assertEquals(null, $this->getParameter('foo'));
    }

    /**
     * Test the inner parameter (used for nested parameter setting / getting).
     */
    public function test_inner_parameter_getter_and_setter()
    {
        // get a inner value
        $innerBar = $this->getInnerParameter('foo', 'bar');

        // assert the return is null, even when when the parent does not exists.
        $this->assertNull($innerBar);

        // set a inner parameter, on a non existing key.
        $innerSetReturn = $this->setInnerParameter('foo', 'bar', 'baz');

        // assert the fluent return from setter.
        $this->assertSame($this, $innerSetReturn);

        // get a inner value
        $innerBar = $this->getInnerParameter('foo', 'bar');

        // assert the value was set and get be retrieved just fine.
        $this->assertEquals('baz', $innerBar);
    }

    /**
     * Test a inner parameter forget.
     */
    public function test_inner_parameter_forget()
    {
        // set a inner parameter.
        $this->setInnerParameter('foo', 'bar', 'baz');

        // forget the inner parameter.
        $forgetInnerReturn = $this->forgetInnerParameter('foo', 'bar');

        // forget should return true case the forget key no longer exists.
        $this->assertTrue($forgetInnerReturn);

        // assert the value is now null.
        $this->assertNull($this->getInnerParameter('foo', 'bar'));
    }
}