<?php

namespace SteemConnect\Http;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use SteemConnect\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use Mockery;

/**
 * Class ClientTest.
 *
 * Http client tests.
 */
class ClientTest extends TestCase
{
    /**
     * Mock an access token.
     *
     * @return Mockery\MockInterface|Token
     */
    protected function mockAccessToken()
    {
        // create an access token mock.
        $token = Mockery::mock(Token::class);

        /** @var Token $token */
        return $token;
    }

    /**
     * Mock a configuration instance.
     *
     * @return Mockery\MockInterface|Config
     */
    protected function mockConfiguration()
    {
        // generate a configuration mock.
        $config = Mockery::mock(Config::class);

        /** @var Config $config */
        return $config;
    }

    /**
     * Mock the internal HTTP client.
     *
     * @return Mockery\MockInterface|ClientInterface
     */
    protected function mockInternalHttpClient()
    {
        // generate a configuration mock.
        $httpClient = Mockery::mock(ClientInterface::class);

        /** @var ClientInterface $client */
        return $httpClient;
    }

    /**
     * Test the custom http client (internal).
     */
    public function test_custom_http_client()
    {
        // generate a new client.
        $client = new Client($this->mockConfiguration());

        // start an empty guzzle http client.
        $customGuzzle = new GuzzleClient();

        // set the client.
        $client->setHttpClient($customGuzzle);

        // assert the instances are the same.
        $this->assertSame($customGuzzle, $client->getHttpClient());
    }

    /**
     * Test the customization of the configuration instance on the Http client.
     */
    public function test_custom_configuration()
    {
        // creates a client instance with default config.
        $client = new Client($this->mockConfiguration());

        // get the default config, for later comparison.
        $defaultConfig = $client->getConfig();

        // creates a new config, to customize on the client.
        $config = new Config('foo', 'bar');

        // set the new config instance.
        $client->setConfig($config);

        // assert the config instance is the customized one.
        $this->assertSame($client->getConfig(), $config);

        // assert the default configuration is not the one on the client instance anymore.
        $this->assertNotSame($client->getConfig(), $defaultConfig);
    }

    /**
     * Test customization of the access token on the client instance.
     */
    public function test_custom_access_token()
    {
        // creates a new client instance.
        $client = new Client($this->mockConfiguration());

        // get a token mock.
        $token = $this->mockAccessToken();

        // customize the access token on the client with the mock
        $setReturn = $client->setAccessToken($token);

        // assert the fluent return.
        $this->assertSame($client, $setReturn);

        // assert the custom token was set on the client instance.
        $this->assertSame($client->getAccessToken(), $token);
    }

    /**
     * Test all constructor customizations.
     */
    public function test_constructor_with_optional_parameters()
    {
        // get a config mock.
        $config = $this->mockConfiguration();
        // get an access token mock.
        $token = $this->mockAccessToken();
        // get an internal http mock (guzzle)
        $internalHttpClient = $this->mockInternalHttpClient();

        // create a new client instance, using custom token and internal http client.
        $client = new Client($config, $token, $internalHttpClient);

        // assert the configuration instance.
        $this->assertSame($config, $client->getConfig());

        // assert the same access token instance.
        $this->assertSame($token, $client->getAccessToken());

        // assert the same http client.
        $this->assertSame($internalHttpClient, $client->getHttpClient());
    }

    /**
     * Simple test on the actual HTTP call.
     *
     * @throws
     */
    public function test_headers_and_http_call()
    {
        // mock the configuration.
        $config = $this->mockConfiguration();

        // the configuration must receive the build url method.
        $config->shouldReceive('buildUrl')->withArgs(['foo'])->andReturn('http://foo.bar/foo');

        // mock the access token.
        $token = $this->mockAccessToken();

        $token->shouldReceive('getToken')->andReturn('bar');

        // mock the internal client.
        $internalClient = $this->mockInternalHttpClient();

        $internalClient->shouldReceive('send')->andReturn(Mockery::mock(ResponseInterface::class));

        // creates a client with the customized mocks
        $client = new Client($config, $token, $internalClient);

        // make a call.
        $response = $client->call('POST', 'foo');

        // assert a response was returned.
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}