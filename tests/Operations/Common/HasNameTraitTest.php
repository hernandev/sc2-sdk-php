<?php

namespace SteemConnect\Operations\Common;

use SteemConnect\TestCase;

/**
 * Class DumbClass.
 *
 * Dumb class declaration.
 *
 */
class DumbClass {
    use HasName;
}

/**
 * Class HasNameTraitTest.
 *
 * Tests for the HasName trait.
 */
class HasNameTraitTest extends TestCase
{
    /**
     * Creates a dumb class instance.
     *
     * @return DumbClass
     */
    protected function getDumbInstance()
    {
        return new DumbClass();
    }

    /**
     * Tests for the name getter.
     */
    public function test_name_getter_and_setter()
    {
        // get a dumb instance that uses the trait.
        $dumb = $this->getDumbInstance();

        // assert both getter and setter exists.
        $this->assertTrue(method_exists($dumb, 'getName'));
        $this->assertTrue(method_exists($dumb, 'setName'));

        // assert no default name exists.
        $this->assertNull($dumb->getName());

        // call the setter and hold it's return.
        $setReturn = $dumb->setName('foo');

        // assert the fluent return.
        $this->assertSame($dumb, $setReturn);

        // now assert the actual content was correctly set.
        $this->assertEquals('foo', $dumb->getName());
    }
}