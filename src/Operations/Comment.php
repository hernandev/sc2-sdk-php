<?php

namespace SteemConnect\Operations;

use Illuminate\Support\Str;

/**
 * Class Comment.
 *
 * Comment operation implementation.
 *
 * @property string $parent_author
 * @property string $parent_permlink
 * @property string $author
 * @property string $permlink
 * @property array  $json_metadata
 * @property string $title
 * @property string $body
 * @property array $tags
 * @property string $app
 * @property string $community
 */
class Comment extends Operation
{
    /**
     * @var array Default parameters.
     */
    protected $parameters = [
        'parent_author' => '',
        'parent_permlink' => null,
        'json_metadata' => [],
    ];

    /**
     * @var array List of parameters that must be treated as JSON.
     */
    protected $jsonParameters = [
        'json_metadata'
    ];

    /**
     * Comment Operation constructor.
     *
     * @param array $parameters Parse parameters directly from constructor.
     */
    public function __construct(array $parameters = [])
    {
        // call parent constructor.
        parent::__construct('comment', array_merge($this->parameters, $parameters));

    }

    /**
     * Parent Author parameter setter.
     *
     * @param string $parentAuthor
     *
     * @return $this
     */
    public function parentAuthor(string $parentAuthor) : self
    {
        return $this->setParameter('parent_author', $parentAuthor);
    }

    /**
     * Parent PermLink parameter setter.
     *
     * @param string $parentPermLink
     *
     * @return $this
     */
    public function parentPermLink(string $parentPermLink) : self
    {
        return $this->setParameter('parent_permlink', $parentPermLink);
    }

    /**
     * Reply to post or comment.
     *
     * @param string $author
     * @param string $permLink
     *
     * @return self
     */
    public function reply(string $author, string $permLink) : self
    {
        return $this->parentAuthor($author)->parentPermLink($permLink);
    }

    /**
     * Author parameter setter.
     *
     * @param string $author
     *
     * @return $this
     */
    public function author(string $author) : self
    {
        return $this->setParameter('author', $author);
    }

    /**
     * PermLink parameter setter.
     *
     * @param string $permLink
     *
     * @return $this
     */
    public function permLink(string $permLink) : self
    {
        return $this->setParameter('permlink', $permLink);
    }

    /**
     * Title parameter setter.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title) : self
    {
        if (!$this->getParameter('permlink')) {
            $this->setParameter('permlink', Str::slug($title));
        }

        return $this->setParameter('title', $title);
    }

    /**
     * Body parameter setter.
     *
     * @param string $body
     *
     * @return $this
     */
    public function body(string $body) : self
    {
        $randomTitle = Str::limit($body, 150);

        if (!$this->getParameter('title')) {
            $this->title($randomTitle);
        }

        return $this->setParameter('body', $body);
    }

    /**
     * Category on posts == parent_permlink.
     *
     * So this is an alias, the category functionality does not exactly exists.
     *
     * @param string|null $category
     *
     * @return self
     */
    public function category(string $category = null) :self
    {
        return $this->parentPermLink($category);
    }

    /**
     * Set the tags metadata.
     *
     * @param array $tags List of tags to add.
     *
     * @return self
     */
    public function tags(array $tags) : self
    {
        return $this->setParameter('json_metadata.tags', $tags);
    }


    /**
     * Set the community name.
     *
     * @param string|null $community
     *
     * @return Comment
     */
    public function community(string $community = null) : self
    {
        return $this->setParameter('json_metadata.community', $community);
    }

    /**
     * Set the application name.
     *
     * @param string $app
     *
     * @return self
     */
    public function app(string $app) : self
    {
        return $this->setParameter('json_metadata.app', $app);
    }

    /**
     * Metadata parameter setter.
     *
     * @param string|array $jsonMetadata
     *
     * @return $this
     */
    public function jsonMetadata($jsonMetadata) : self
    {
        // convert into array, if json.
        $metadata = is_string($jsonMetadata) ? json_decode($jsonMetadata, true) : $jsonMetadata;

        // set the json metadata as string or array
        return $this->setParameter('json_metadata', $metadata);
    }
}