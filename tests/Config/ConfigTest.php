<?php

namespace SteemConnect\Config;

use SteemConnect\TestCase;
use SteemConnect\OAuth2\Config\Config as OAuthConfig;

/**
 * Class ConfigTest.
 *
 * Tests for the Config class.
 */
class ConfigTest extends TestCase
{
    /**
     * Factories a default config instance for testing.
     *
     * @return Config
     */
    protected function factoryConfig()
    {
        return new Config($this->clientId, $this->clientSecret);
    }

    /**
     * Test the construction and the inheritance of the config instance.
     */
    public function test_construction()
    {
        // generate a local instance of the config class.
        // passing the default client and secret for testing.
        $config = new Config($this->clientId, $this->clientSecret);

        // assert the client id and secret were set correctly.
        $this->assertEquals($config->getClientId(), $this->clientId);
        $this->assertEquals($config->getClientSecret(), $this->clientSecret);

        // assert the instance if actually the SDK config.
        $this->assertInstanceOf(Config::class, $config);
        // assert the config class inherits the OAuth config class.
        $this->assertInstanceOf(OAuthConfig::class, $config);
    }

    /**
     * Test the default application name on the SDK.
     */
    public function test_default_app_name()
    {
        // get a default config instance from factory.
        $config = $this->factoryConfig();

        // assert the default application name.
        $this->assertEquals('sc2-php-sdk/1.0', $config->getApp());
    }

    /**
     * Test the customizations on the application name/version.
     */
    public function test_customization_of_app_name()
    {
        // factory a default configuration instance.
        $config = $this->factoryConfig();

        // customize the application name.
        $setReturn = $config->setApp('testing/0.10.0');

        // assert fluent return.
        $this->assertSame($config, $setReturn);

        // assert the customized application name was actually set on the instance
        $this->assertEquals('testing/0.10.0', $config->getApp());
    }

    /**
     * Test the default community on the configuration.
     */
    public function test_default_community_is_empty()
    {
        // factory config.
        $config = $this->factoryConfig();

        // the default community should be null.
        $this->assertNull($config->getCommunity());
    }

    /**
     * Test the custom community setting.
     */
    public function test_customization_of_community()
    {
        // factory config.
        $config = $this->factoryConfig();

        // customize the community.
        $setReturn = $config->setCommunity('some-community');

        // assert the return is fluent.
        $this->assertSame($config, $setReturn);

        // the default community should be null.
        $this->assertEquals('some-community', $config->getCommunity());
    }
}