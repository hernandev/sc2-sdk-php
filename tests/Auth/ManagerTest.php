<?php

namespace SteemConnect\Auth;

use Mockery;
use SteemConnect\Config\Config;
use SteemConnect\Exceptions\TokenException;
use SteemConnect\OAuth2\Provider\Provider;
use SteemConnect\TestCase;

/**
 * Class ManagerTest.
 *
 * Tests for auth Manager class.
 */
class ManagerTest extends TestCase
{
    /**
     * Mock a configuration instance.
     *
     * @return Mockery\MockInterface|Config
     */
    protected function mockConfig()
    {
        return Mockery::mock(Config::class);
    }

    /**
     * Mock the OAuth provider class.
     *
     * @return Mockery\MockInterface|Provider
     */
    protected function mockProvider()
    {
        return Mockery::mock(Provider::class);
    }

    /**
     * Mock an access token.
     *
     * @return Mockery\MockInterface|Token
     */
    protected function mockAccessToken()
    {
        return Mockery::mock(Token::class);
    }

    /**
     * Test the authorization url building, that should match the provider url.
     */
    public function test_authorization_url_building()
    {
        // start a mock oauth provider.
        $provider = $this->mockProvider();
        // mock the authorization method to a given return.
        $provider->shouldReceive('getAuthorizationUrl')->andReturn('https://foo.bar/authorization');

        // start a manager instance.
        $manager = new Manager($this->mockConfig(), $provider);

        // assert the url building matches.
        $this->assertEquals($manager->getAuthorizationUrl(), 'https://foo.bar/authorization');
    }

    /**
     * Test token parsing from callback.
     */
    public function test_parsing_return()
    {
        // start a mock oauth provider.
        $provider = $this->mockProvider();

        // mock an access token.
        $mockToken = $this->mockAccessToken();

        // mock the JSON serialization.
        $mockToken->shouldReceive('jsonSerialize')->andReturn([ "access_token" => "foo"]);

        // mock the parse return method.
        $provider->shouldReceive('parseReturn')->andReturn($mockToken);

        // start a manager instance.
        $manager = new Manager($this->mockConfig(), $provider);

        // parse the return from oauth.
        $token = $manager->parseReturn();

        // assert the token was returned properly.
        $this->assertEquals("foo", $token->getToken());
    }

    /**
     * Test the custom exception throw from parse error (token exception).
     */
    public function test_parsing_return_error_custom_exception()
    {
        // start a mock oauth provider.
        $provider = $this->mockProvider();

        // the provider should throw an exception when the
        // parse return method is called.
        $provider->shouldReceive('parseReturn')->andReturnUsing(function () {
            throw new \Exception();
        });

        // start a manager instance.
        $manager = new Manager($this->mockConfig(), $provider);

        // try the parse.
        try {
            // this will throw an exception.
            $manager->parseReturn();
        } catch (\Exception $e) {
            // the exception must be of the TokenException type.
            $this->assertInstanceOf(TokenException::class, $e);
        }
    }
}