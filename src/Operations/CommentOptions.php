<?php

namespace SteemConnect\Operations;

/**
 * Class CommentOptions.
 *
 * Comment options operation implementation.
 */
class CommentOptions extends Operation
{
    /**
     * @var array Default parameters.
     */
    protected $parameters = [

    ];

    /**
     * Comment Operation constructor.
     *
     * @param array $parameters Parse parameters directly from constructor.
     */
    public function __construct(array $parameters = [])
    {
        // call parent constructor.
        parent::__construct('comment_options', array_merge($this->parameters, $parameters));
    }

    /**
     * Set author and permlink from a comment instance.
     *
     * @param Comment $comment
     *
     * @return self
     */
    public function of(Comment $comment) : self
    {
        return $this->author($comment->author)->permLink($comment->permlink);
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
     * Set the max payout for the comment.
     *
     * @param float $maxPayout
     *
     * @return self
     */
    public function maxAcceptedPayout(float $maxPayout) : self
    {
        // format the number with 3 decimal digits.
        $max = number_format($maxPayout, 3, '.', '');

        return $this->setParameter('max_accepted_payout', "{$max} SBD");
    }

    /**
     * @param $value
     *
     * @return self
     */
    public function percentSteemDollars($value) : self
    {
        return $this->setParameter('percent_steem_dollars', $value);
    }

    /**
     * @param bool $allowVotes
     *
     * @return self
     */
    public function allowVotes(bool $allowVotes = true) : self
    {
        return $this->setParameter('allow_votes', $allowVotes);
    }

    /**
     * @param bool $allowCuration
     *
     * @return self
     */
    public function allowCurationRewards(bool $allowCuration = true) : self
    {
        return $this->setParameter('allow_curation_rewards', $allowCuration);
    }
}