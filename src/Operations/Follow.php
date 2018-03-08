<?php

namespace SteemConnect\Operations;

/**
 * Class Follow.
 *
 * Follow extends CustomJson and set it's own parameters.
 *
 * @property string $id
 * @property string $follower
 * @property string $following
 * @property array $what
 */
class Follow extends CustomJson
{
    /**
     * @var string Inner json operation name.
     */
    protected $customOperation = 'follow';

    /**
     * Follow constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        // call parent constructor.
        parent::__construct($parameters);

        // set id on the custom json operation, if not already defined on parameters.
        if (!$this->getId()) {
            $this->setId('follow');
        }
    }

    /**
     * Alias for the follower account.
     *
     * @param string $account
     *
     * @return Follow
     */
    public function account(string $account): self
    {
        return $this->follower($account);
    }


    /**
     * Set the follower name.
     *
     * @param string $follower
     *
     * @return self
     */
    public function follower(string $follower) : self
    {
        $this->setRequiredPostingAuths([$follower]);

        $this->getSubOperation()->setParameter('follower', $follower);

        return $this;
    }

    /**
     * Set the user that will be followed.
     *
     * @param string $account
     * @param bool $following
     *
     * @return self
     */
    public function following(string $account, bool $following = true) : self
    {
        // set the account to follow or unfollow.
        $this->getSubOperation()->setParameter('following', $account);

        // determine the what section based on the following flag,
        $what = $following ? ['blog'] : [];

        // set the what parameter.
        $this->getSubOperation()->setParameter('what', $what);

        // return it.
        return $this;
    }

    /**
     * Alias for follow.
     *
     * @param string $account
     *
     * @return self
     */
    public function follow(string $account) : self
    {
        return $this->following($account, true);
    }

    /**
     * Alias for unfollow.
     *
     * @param string $account
     *
     * @return self
     */
    public function unfollow(string $account) : self
    {
        return $this->following($account, false);
    }
}