<?php

namespace SteemConnect\Operations;

use SteemConnect\TestCase;

/**
 * Class VoteTest.
 *
 * Tests for the Vote operation class.
 */
class VoteTest extends TestCase
{
    /**
     * Test the operation setters.
     */
    public function test_setters()
    {
        // start a vote instance.
        $vote = new Vote();

        // assert set and get.
        $vote->voter('some-voter');
        $this->assertEquals('some-voter', $vote->voter);

        // assert set and get.
        $vote->account('some-voter-account');
        $this->assertEquals('some-voter-account', $vote->voter);

        // assert set and get.
        $vote->author('author');
        $this->assertEquals('author', $vote->author);

        // assert set and get.
        $vote->permLink('some-post');
        $this->assertEquals('some-post', $vote->permlink);

        // assert set and get.
        $vote->on('user', 'post-link');
        $this->assertEquals('user', $vote->author);
        $this->assertEquals('post-link', $vote->permlink);

        // weight tests.
        $vote->weight(1);
        $this->assertEquals(10000, $vote->weight);

        // normal units
        $vote->weight(10000);
        $this->assertEquals(10000, $vote->weight);

        // percent alias.
        $vote->percent(0.5);
        $this->assertEquals(5000, $vote->weight);

        // test positive value on downvote.
        $vote->downVote(0.5);
        $this->assertEquals(-5000, $vote->weight);

        // test negative value on downvote.
        $vote->downVote(-5000);
        $this->assertEquals(-5000, $vote->weight);

        // test upvote.
        $vote->upVote(-5000);
        $this->assertEquals(5000, $vote->weight);

        // test upvote.
        $vote->upVote(0.7);
        $this->assertEquals(7000, $vote->weight);
    }
}