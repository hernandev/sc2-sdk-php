<?php

namespace SteemConnect\Operations;

/**
 * Class Reblog.
 *
 * Reblog extends CustomJson and set it's own parameters.
 *
 * @property string $id
 * @property string $account
 * @property string $author
 * @property string $permlink
 */
class Reblog extends CustomJson
{
    /**
     * @var string Inner json operation name.
     */
    protected $customOperation = 'reblog';

    /**
     * Reblog constructor.
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
     * Set the follower name.
     *
     * @param string $account
     *
     * @return self
     */
    public function account(string $account) : self
    {
        $this->setRequiredPostingAuths([$account]);

        $this->getSubOperation()->setParameter('account', $account);

        return $this;
    }

    /**
     * Set the follower name.
     *
     * @param string $author
     *
     * @return self
     */
    public function author(string $author) : self
    {
        $this->getSubOperation()->setParameter('author', $author);

        return $this;
    }

    /**
     * Set the follower name.
     *
     * @param string $permLink
     *
     * @return self
     */
    public function permLink(string $permLink) : self
    {
        $this->getSubOperation()->setParameter('permlink', $permLink);

        return $this;
    }

    /**
     * Set the original author username.
     *
     * @param string $author
     * @param string $permLink
     *
     * @return self
     */
    public function reblog(string $author, string $permLink) : self
    {
        return $this->author($author)->permLink($permLink);
    }
}