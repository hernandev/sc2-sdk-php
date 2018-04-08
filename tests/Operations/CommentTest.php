<?php

namespace SteemConnect\Operations;


use SteemConnect\TestCase;

/**
 * Class CommentTest.
 *
 * Tests for the Comment operation class.
 */
class CommentTest extends TestCase
{
    /**
     * Test automatic permlink generation from a given title.
     */
    public function test_title_to_permlink_conversion()
    {
        // start the comment.
        $comment = new Comment();

        // generate a simple title.
        $title = 'This is a title made by a @user!';

        // generate a permlink.
        $permLink = str_slug($title, '-');

        // set the title on the comment
        $comment->title($title);

        // assert a permlink was generated.
        $this->assertEquals($permLink, $comment->permlink);
    }

    /**
     * Test permlink is preserved when present and title is set.
     */
    public function test_permlink_is_preserved()
    {
        // start comment.
        $comment = new Comment();

        // set a custom permlink.
        $comment->permLink('some-custom-permlink');

        // set comment title.
        $comment->title('foo bar, baz');

        // now assert the title did not replaced the initial permlink.
        $this->assertEquals('some-custom-permlink', $comment->permlink);
    }

    /**
     * Test reply settings.
     */
    public function test_reply_setters()
    {
        // start a comment.
        $comment = new Comment();

        // call the reply alias.
        $comment->reply('some-user', 'post-permlink');

        // assert the parent author and permlink.
        $this->assertEquals('some-user', $comment->parent_author);
        $this->assertEquals('post-permlink', $comment->parent_permlink);

        // start another comment.
        $comment = new Comment();

        // parent author setter.
        $comment->parentAuthor('another-user');
        $this->assertEquals('another-user', $comment->parent_author);

        // parent permlink setter.
        $comment->parentPermLink('another-post-permlink');
        $this->assertEquals('another-post-permlink', $comment->parent_permlink);
    }

    /**
     * Test author and category.
     */
    public function test_author_and_category()
    {
        // start a comment.
        $comment = new Comment();

        // call the category alias.
        $comment->category('cool-topic');

        // assert the parent permlink.
        $this->assertEquals('cool-topic', $comment->parent_permlink);
        // parent author should start as empty string.
        $this->assertEquals('', $comment->parent_author);

        // set the author.
        $comment->author('the-author');
        // assert it's value.
        $this->assertEquals('the-author', $comment->author);
    }

    /**
     * Test tags.
     */
    public function test_tags()
    {
        // start a comment.
        $comment = new Comment();

        // set the tags on the comment.
        $comment->tags(['foo', 'bar', 'baz']);

        // assert the json meta value was set.
        $this->assertEquals(['foo', 'bar', 'baz'], $comment->tags);
    }

    /**
     * Customize application and community on a given comment.
     */
    public function test_app_and_community()
    {
        // start a comment.
        $comment = new Comment();

        // set the app name.
        $comment->app('foobar/9.0');

        // set community.
        $comment->community('steemdev');

        // assert the json meta value was set.
        $this->assertEquals('foobar/9.0', $comment->app);

        // assert the json meta value was set.
        $this->assertEquals('steemdev', $comment->community);
    }

    /**
     * Test direct setting json metadata.
     */
    public function test_direct_json_meta_parsing()
    {
        // start a comment
        $comment = new Comment();

        // set the json metadata.
        $comment->jsonMetadata(['foo' => 'bar', 'tags' => ['a', 'b']]);

        // assert the tags set from the direct array passing.
        $this->assertEquals(['a', 'b'], $comment->tags);
    }

    /**
     * Tests related to comment content (body).
     */
    public function test_body_set_and_parsing()
    {
        // start the comment.
        $comment = new Comment();

        // post body
        $comment->body('this is a body ok?');

        // assert a slug was generated for the permlink from the content body.
        $this->assertEquals('this-is-a-body-ok', $comment->permlink);
    }
}