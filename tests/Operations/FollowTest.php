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
}