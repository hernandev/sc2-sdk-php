<?php

namespace SteemConnect\Operations;

/**
 * Class Vote.
 *
 * Vote operation implementation.
 *
 * @property string $voter
 * @property string $author
 * @property string $permlink
 * @property int    $weight
 */
class Vote extends Operation
{
    /**
     * Vote operation constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        // cal parent constructor.
        parent::__construct('vote', array_merge($this->parameters, $parameters));
    }

    /**
     * Voter/account parameter setter.
     *
     * @param string $voter
     *
     * @return self
     */
    public function voter(string $voter) : self
    {
        return $this->setParameter('voter', $voter);
    }

    /**
     * Alias for the voter setter.
     *
     * @param string $account
     *
     * @return self
     */
    public function account(string $account) : self
    {
        return $this->voter($account);
    }

    /**
     * Author parameter setter.
     *
     * @param string $author
     *
     * @return self
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
     * @return self
     */
    public function permLink(string $permLink) : self
    {
        return $this->setParameter('permlink', $permLink);
    }

    /**
     * Simple vote author & permlink alias.
     *
     * @param string $author
     * @param string $permlink
     *
     * @return self
     */
    public function on(string $author, string $permlink) : self
    {
        return $this->author($author)->permLink($permlink);
    }

    /**
     * Up vote alias.
     *
     * @param int|float $weight
     *
     * @return self
     */
    public function upVote($weight = 1) : self
    {
        return $this->weight(abs($weight));
    }

    /**
     * Down vote alias.
     *
     * @param int|float $weight
     *
     * @return self
     */
    public function downVote($weight = -1) : self
    {
        return $this->weight(abs($weight) * -1);
    }

    /**
     * Weight parameter setter.
     *
     * Notice: This value should be a float between 0 and 1 if integers are disabled in configuration.
     *
     * @param float|int $weight For integers usage, pass 7500 for 75%, for decimal, pass 0.75 for 75%.
     *
     * @return self
     */
    public function weight($weight) : self
    {
        // detect decimal vs big integer for vote weight.
        if (abs($weight) > 0 && abs($weight) <= 1) {
            $realWeight = $weight * 10000;
        } else {
            $realWeight = $weight;
        }

        return $this->setParameter('weight', $realWeight);
    }

    /**
     * Alias for weight setter.
     *
     * @param $percent
     *
     * @return self
     */
    public function percent($percent) : self
    {
        return $this->weight($percent);
    }
}