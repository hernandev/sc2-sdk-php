<?php

namespace SteemConnect\Client;

use Mockery;
use SteemConnect\Auth\Manager;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use SteemConnect\OAuth2\Provider\Provider;
use SteemConnect\Operations\Operation;
use SteemConnect\TestCase;
use SteemConnect\Http\Client as HttpClient;

/**
 * Class ClientTest.
 *
 * Tests for the SDK client class.
 */
class ClientTest extends TestCase
{
    /**
     * Mock the config class.
     *
     * @return Mockery\MockInterface|Config
     */
    protected function mockConfig()
    {
        // start a config mock.
        $config = Mockery::mock(Config::class);

        // client id, client secret and return URL mock methods.
        $config->shouldReceive('getClientId')->andReturn('foo');
        $config->shouldReceive('getClientSecret')->andReturn('bar');
        $config->shouldReceive('getReturnUrl')->andReturn('https://foo.bar/callback');

        // return the config mock.
        return $config;
    }

    /**
     * Mock the OAuth provider.
     *
     * @return Mockery\MockInterface|Provider
     */
    protected function mockProvider()
    {
        // start the provider mock.
        $provider = Mockery::mock(Provider::class);

        // return the provider.
        return $provider;
    }

    /**
     * Mock an access token.
     *
     * @return Mockery\MockInterface|Token
     */
    protected function mockToken()
    {
        // start a token mock.
        $token = Mockery::mock(Token::class);

        // when asked about, return a fake token string (access token).
        $token->shouldReceive('getToken')->andReturn('foo-bar-baz-token');

        // return the token mock.
        return $token;
    }

    /**
     * Simple constructor test.
     */
    public function test_construction()
    {
        // get a config mock.
        $config = $this->mockConfig();

        // starts the client with the config mock.
        $client = new Client($config);

        // test the config instance matches.
        $this->assertSame($config, $client->getConfig());

        // assets the constructor created an http client instance.
        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
    }

    /**
     * Test token getter and setter on the SDK client.
     */
    public function test_token_getter_and_setter()
    {
        // start a client instance.
        $client = new Client($this->mockConfig());

        // assert the token starts as null.
        $this->assertNull($client->getToken());

        // get a mock access token.
        $token = $this->mockToken();

        // set the custom token on the SDK client.
        $setReturn = $client->setToken($token);

        // assert the set token has a fluent return.
        $this->assertSame($client, $setReturn);

        // assert the instance was correctly set on the SDK client.
        $this->assertSame($token, $client->getToken());
    }

    /**
     * Test token getter and setter on the SDK client.
     */
    public function test_provider_getter_and_setter()
    {
        // start a client instance.
        $client = new Client($this->mockConfig());

        // start a mock provider.
        $provider = $this->mockProvider();

        // set the custom provider on the SDK client.
        $setReturn = $client->setOAuthProvider($provider);

        // assert the set provider has a fluent return.
        $this->assertSame($client, $setReturn);

        // assert the instance was correctly set on the SDK client.
        $this->assertSame($provider, $client->getOAuthProvider());
    }

    /**
     * Test auth manager instantiation.
     */
    public function test_auth_manager_instance()
    {
        // start a client instance.
        $client = new Client($this->mockConfig());

        // assert the instance of Manager from auth method.
        $this->assertInstanceOf(Manager::class, $client->auth());
    }

    /**
     * Test operation broadcasting from the client.
     */
    public function test_broadcasting()
    {
        // start a broadcaster mock.
        /** @var Broadcaster|Mockery\MockInterface $broadcaster */
        $broadcaster = Mockery::mock(Broadcaster::class);
        $broadcaster->shouldReceive('setHttpClient')->andReturn($broadcaster);
        $broadcaster->shouldReceive('setConfig')->andReturn($broadcaster);
        $broadcaster->shouldReceive('setToken')->andReturn($broadcaster);

        /** @var Operation|Mockery\MockInterface $operation */
        $operation = Mockery::mock(Operation::class);

        /** @var Response|Mockery\MockInterface $response */
        $response = Mockery::mock(Response::class);

        // main broadcast method mock.
        $broadcaster->shouldReceive('broadcast')->andReturn($response);

        // start a client with mock config.
        $client = new Client($this->mockConfig());
        // pass the mock token into the client.
        $client->setToken($this->mockToken());

        // customize the broadcaster instance
        $client->setBroadcaster($broadcaster);

        // call the broadcast method and get the response.
        $broadcastResponse = $client->broadcast($operation);

        // assert the return is the same.
        $this->assertSame($response, $broadcastResponse);
    }

    /**
     * Test the client refresh methods.
     */
    public function test_refresh_methods()
    {
        // start a SDK client.
        $client = new Client($this->mockConfig());
        // set a mock token.
        $client->setToken($this->mockToken());

        // call the refresh method.
        $client->refreshBroadcaster();

        // assert the broadcaster was created.
        $this->assertInstanceOf(Broadcaster::class, $client->getBroadcaster());

    }
}