<?php

namespace SteemConnect\Operations;

use SteemConnect\TestCase;

/**
 * Class CommentOptionsTest.
 *
 * Tests for the CommentOptions operation class.
 */
class CommentOptionsTest extends TestCase
{
    /**
     * Test a comment pair attributes setting on a comment options instance.
     */
    public function test_setter_of_comment_pair()
    {
        // start a comment.
        $comment = new Comment();

        // set the comment author and permlink.
        $comment->author('foo-user');
        $comment->permLink('foo-permlink');

        // start the comment options.
        $commentOptions = new CommentOptions();

        // set the comment operation pair.
        $commentOptions->of($comment);

        // assert the author and permlink were set from the comment.
        $this->assertEquals('foo-user', $commentOptions->author);
        $this->assertEquals('foo-permlink', $commentOptions->permlink);
    }

    /**
     * Test the setting of another options.
     */
    public function test_options_setters()
    {
        // start a comment.
        $comment = new Comment();

        // set the comment author and permlink.
        $comment->author('foo-user');
        $comment->permLink('foo-permlink');

        // start the comment options.
        $commentOptions = new CommentOptions();

        // disable curation.
        $commentOptions->allowCurationRewards(false);
        // assert it was set as false.
        $this->assertFalse($commentOptions->allow_curation_rewards);

        // disable votes.
        $commentOptions->allowVotes(false);
        // assert it was set as false.
        $this->assertFalse($commentOptions->allow_votes);

        // set the max accepted payout.
        $commentOptions->maxAcceptedPayout(10);
        // assert the value formatting.
        $this->assertEquals('10.000 SBD', $commentOptions->max_accepted_payout);

        // set the SBD percent.
        $commentOptions->percentSteemDollars(9000);
        // assert the correct value.
        $this->assertEquals(9000, $commentOptions->percent_steem_dollars);
    }
}