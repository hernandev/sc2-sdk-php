<?php

namespace SteemConnect\Config;

use SteemConnect\OAuth2\Config\Config as OAuthConfig;

/**
 * Class Config.
 *
 * All things related to SteemConnect will be configured through this class.
 *
 * If you are using the SDK, no call to the original OAuth2 client is required since
 * it will be wrapped on this SDK.
 */
class Config extends OAuthConfig
{
    /**
     * @var null|string Application name.
     */
    protected $app = 'sc2-php-sdk/1.0';

    /**
     * @var null|string Community name.
     */
    protected $community;

    /**
     * Customize the application name (metadata on posts).
     *
     * @param string $app
     *
     * @return self
     */
    public function setApp(string $app = null) : self
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Returns the current application name configured.
     *
     * @return null|string
     */
    public function getApp() : ?string
    {
        return $this->app;
    }

    /**
     * Set the community using the SDK.
     *
     * @param string|null $community
     *
     * @return self
     */
    public function setCommunity(string $community = null) : self
    {
        $this->community = $community;

        return $this;
    }

    /**
     * Returns the community using the SDK.
     *
     * @return null|string
     */
    public function getCommunity() : ?string
    {
        return $this->community;
    }
}