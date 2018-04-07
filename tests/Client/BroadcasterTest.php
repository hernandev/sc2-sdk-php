<?php

namespace SteemConnect\Client;

use Mockery;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use SteemConnect\TestCase;
use SteemConnect\Http\Client as HttpClient;

/**
 * Class BroadcasterTest.
 *
 * Tests for the broadcaster class.
 */
class BroadcasterTest extends TestCase
{
    /**
     * Create a mock of Configuration.
     *
     * @return Mockery\MockInterface|Config
     */
    protected function mockConfig()
    {
        return Mockery::mock(Config::class);
    }

    /**
     * Create a mock of Token.
     *
     * @return Mockery\MockInterface|Token
     */
    protected function mockToken()
    {
        return Mockery::mock(Token::class);
    }

    /**
     * Create a mock of HttpClient.
     *
     * @return Mockery\MockInterface|HttpClient
     */
    protected function mockHttpClient()
    {
        return Mockery::mock(HttpClient::class);
    }

    public function test_construction()
    {
        $config = $this->mockConfig();
        $token = $this->mockToken();
        $httpClient = $this->mockHttpClient();

        $broadcaster = new Broadcaster($config, $token, $httpClient);

        $this->assertSame($config, $broadcaster->getConfig());
        $this->assertSame($token, $broadcaster->getToken());
        $this->assertSame($httpClient, $broadcaster->getHttpClient());


    }
}