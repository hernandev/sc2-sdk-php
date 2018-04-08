<?php

namespace SteemConnect\Operations;


use SteemConnect\TestCase;

/**
 * Class FollowTest.
 *
 * Tests for the Follow operation class.
 */
class FollowTest extends TestCase
{
    /**
     * Test the operation setters.
     */
    public function test_setters()
    {
        // start a follow operation instance.
        $follow = new Follow();

        // set and test (account alias to follower).
        $follow->account('some-account');
        $this->assertEquals('some-account', $follow->follower);

        // set and test.
        $follow->follower('follower-account');
        $this->assertEquals('follower-account', $follow->follower);

        // set and test.
        $follow->following('following', true);
        $this->assertEquals('following', $follow->following);
        $this->assertEquals(['blog'], $follow->what);

        // set and test.
        $follow->following('following', false);
        $this->assertEquals('following', $follow->following);
        $this->assertEquals([], $follow->what);

        // reset the follow instance.
        $follow = new Follow();

        // follow alias test.
        $follow->follow('others');
        $this->assertEquals('others', $follow->following);
        $this->assertEquals(['blog'], $follow->what);

        // unfollow alias test.
        $follow->unfollow('others');
        $this->assertEquals('others', $follow->following);
        $this->assertEquals([], $follow->what);

        $follow->following('other', true);
    }

    /**
     * Test the getter on a sub operation property.
     */
    public function test_defined_sub_operation_property()
    {
        // start a follow operation.
        $follow = new Follow();

        // use the magic setter against a already defined property.
        $this->assertNotNull($follow->getSubOperation()->parameters);
    }

    /**
     * Required auths and posting auths testing.
     */
    public function test_required_auths()
    {
        // start a follow operation.
        $follow = new Follow();

        // required auths getter / setter tests.
        $this->assertEquals([], $follow->getRequiredAuths());
        $this->assertEquals([], $follow->getRequiredPostingAuths());

        // custom the auths.
        $follow->setRequiredAuths(['a', 'b']);
        $follow->setRequiredPostingAuths(['c', 'd']);

        // required auths getter tests.
        $this->assertEquals(['a', 'b'], $follow->getRequiredAuths());
        $this->assertEquals(['c', 'd'], $follow->getRequiredPostingAuths());
    }

    /**
     * Sub operation testing.
     */
    public function test_sub_operations()
    {
        // start a follow operation.
        $follow = new Follow();

        // get the sub operation (currently empty);
        $subOperation = $follow->getSubOperation();
        // set a given parameter/
        $subOperation->setParameter('foo', 'bar');

        // set the sub operation back.
        $follow->setSubOperation($subOperation);

        // call the magic getter to extract the value.
        $this->assertEquals('bar', $follow->foo);
    }

    /**
     * Test direct parameters getter.
     */
    public function test_magic_getter_against_local_property()
    {
        // start a follow operation.
        $follow = new Follow();

        // test the getter and it's value.
        $this->assertEquals('follow', $follow->customOperation);
    }

    /**
     * Assert parsing JSON string on the constructor.
     */
    public function test_json_string_parsing_on_constructor()
    {
        // dummy operation data.
        $operation = [
            'json' => json_encode(['foo' => 'bar'])
        ];

        // start a follow instance, passing some data.
        $follow = new Follow($operation);

        // assert the value.
        $this->assertEquals('bar', $follow->foo);
    }
}