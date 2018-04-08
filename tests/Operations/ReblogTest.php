<?php

namespace SteemConnect\Operations;


use SteemConnect\TestCase;

/**
 * Class ReblogTest.
 *
 * Tests for the Reblog operation class.
 */
class ReblogTest extends TestCase
{
    /**
     * Test the operation setters.
     */
    public function test_setters()
    {
        // start a reblog instance.
        $reblog = new Reblog();

        // assert set and get.
        $reblog->account('testing-reblog-account');
        $this->assertEquals('testing-reblog-account', $reblog->account);

        // assert set and get.
        $reblog->author('testing-author');
        $this->assertEquals('testing-author', $reblog->author);

        // assert set and get.
        $reblog->permLink('some-post');
        $this->assertEquals('some-post', $reblog->permlink);

        // empty instance.
        $reblog = new Reblog();

        // test reblog method.
        $reblog->reblog('test-author', 'test-permlink');
        $this->assertEquals('test-author', $reblog->author);
        $this->assertEquals('test-permlink', $reblog->permlink);
    }
}