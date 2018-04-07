<?php

namespace SteemConnect\Operations\Common;

use SteemConnect\TestCase;

/**
 * Class DumbJsonClass.
 *
 * Dump implementation for testing.
 *
 * Must implement JsonSerializable interface because traits cannot.
 */
class DumbJsonClass implements \JsonSerializable {
    // enable both traits.
    use HasParameters;
    use SerializesJson;

    // set the json parameters at constructor.
    public function __construct()
    {
        $this->jsonParameters = ['foo', 'bar', 'baz'];
    }
}

/**
 * Class SerializesJsonTest.
 *
 * Tests for the SerializesJson trait.
 */
class SerializesJsonTest extends TestCase implements \JsonSerializable
{
    // enable both traits.
    use HasParameters;
    use SerializesJson;

    /**
     * Test serialization (using the test class itself.)
     */
    public function test_json_serialize_calls_array_transform()
    {
        // custom set name.
        $this->setName('foo');

        // some fake data for testing.
        $fakeData = [ 'bar' => 'baz', 'obj' => collect(['a']) ];

        // set some parameters
        $this->parameters = $fakeData;

        // set the name of the json parameters key.
        $this->jsonParameters = 'foo';

        // expected result array format.
        $expectedArray = [
            'foo', $fakeData,
        ];

        // encode the data.
        $expectedJson = json_encode($expectedArray);

        // encoded result
        $json = json_encode($this->toArray());

        // assert both json versions are the same.
        $this->assertEquals($expectedJson, $json);

        // assert the array transformation.
        $this->assertEquals($expectedArray, $this->toArray());
    }

    /**
     * Test for operations / instances that don't have name setters and getter.
     */
    public function test_nameless_serialization()
    {
        // start a dump class instance.
        $dumb = new DumbJsonClass();

        // set some parameters.
        $parameters = [
            'foo' => 'bar',
            'bar' => collect(['baz']),
            'baz' => ['a', 'b', 'c']
        ];

        // create a json version, with external json encoding.
        $parametersJson = json_encode([
            'foo' => 'bar',
            'bar' => json_encode(collect(['baz'])),
            'baz' => json_encode(['a', 'b', 'c'])
        ]);

        // set the parameters on the dump implementation.
        $dumb->setParameters($parameters);

        // finally call the json encode on the dump implementation.
        $jsonSerialized = json_encode($dumb);

        // assert the parameters match after serialization.
        $this->assertEquals($parametersJson, $jsonSerialized);
    }
}