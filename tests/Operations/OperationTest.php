<?php

namespace SteemConnect\Operations;

use SteemConnect\TestCase;

/**
 * Class OperationTest.
 *
 * Tests for the base Operation implementation.
 */
class OperationTest extends TestCase
{
    /**
     * Test magic getters on the base operation implementation.
     */
    public function test_magic_getter()
    {
        // start a dummy operation.
        $operation = new Operation('foo', ['foo' => 'bar']);

        // assert the parameters were correctly set.
        $this->assertEquals($operation->getParameters(), ['foo' => 'bar']);

        // assert the magic getters will retrieve parameters.
        $this->assertEquals('bar', $operation->foo);
        // assert the magic getters will retrieve class attributes as well.
        $this->assertEquals('foo', $operation->name);
    }
}