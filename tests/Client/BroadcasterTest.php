<?php

namespace SteemConnect\Client;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use SteemConnect\Exceptions\ClientException;
use SteemConnect\Exceptions\ResponseException;
use SteemConnect\Operations\Operation;
use SteemConnect\Operations\Vote;
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

    /**
     * Creates a OK http response mock.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function mockHttpResponse()
    {
        // start a mock http response.
        $response = Mockery::mock(ResponseInterface::class);

        // status codes and reason phrase.
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getReasonPhrase')->andReturn('OK');

        // json headers.
        $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => 'application/json']);

        // body.
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"foo":"bar"}');

        $response->shouldReceive('getBody')->andReturn($body);

        return $response;
    }

    /**
     * Creates an error http response mock.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function mockErrorHttpResponse()
    {
        // start a mock http response.
        $response = Mockery::mock(ResponseInterface::class);

        // status codes and reason phrase.
        $response->shouldReceive('getStatusCode')->andReturn(401);
        $response->shouldReceive('getReasonPhrase')->andReturn('Unauthorized');

        // json headers.
        $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => 'application/json']);

        // body mock.
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"error":"YOUR ARE NOT AUTHORIZED"}');

        // set the body mock to respond when the getBody method is called.
        $response->shouldReceive('getBody')->andReturn($body);

        // return the error response.
        return $response;
    }

    /**
     * Returns a broadcaster instance, with all dependencies mocked.
     *
     * @return Broadcaster
     */
    protected function getInstanceWithMocks()
    {
        // get a mock config.
        $config = $this->mockConfig();
        // get a mock token.
        $token = $this->mockToken();
        // get a mock http client.
        $httpClient = $this->mockHttpClient();

        // create a broadcaster instance.
        $broadcaster = new Broadcaster($config, $token, $httpClient);

        // returns the broadcaster.
        return $broadcaster;
    }

    /**
     * Creates a mock operation.
     *
     * @return Mockery\MockInterface|Operation
     */
    protected function mockOperation()
    {
        // start a mock operation.
        $operation = Mockery::mock(Operation::class);

        // make it array compatible.
        $operation->shouldReceive('toArray')->andReturn(['foo' => 'bar']);

        // return the operation itself.
        return $operation;
    }

    /**
     * Test construction.
     */
    public function test_construction()
    {
        // get a mock config.
        $config = $this->mockConfig();
        // get a mock token.
        $token = $this->mockToken();
        // get a mock http client.
        $httpClient = $this->mockHttpClient();

        // create a broadcaster instance.
        $broadcaster = new Broadcaster($config, $token, $httpClient);

        // assert the config was correctly set by constructor.
        $this->assertSame($config, $broadcaster->getConfig());
        // assert the token was correctly set by constructor.
        $this->assertSame($token, $broadcaster->getToken());
        // assert the http client was correctly set by constructor.
        $this->assertSame($httpClient, $broadcaster->getHttpClient());
    }

    /**
     * Test config getter and setter.
     */
    public function test_config_getter_and_setter()
    {
        // create a new config mock.
        $config = $this->mockConfig();

        // creates a new broadcaster instance.
        $broadcaster = $this->getInstanceWithMocks();

        // assert the internal instance is not the same from the one
        // previously created.
        $this->assertNotSame($config, $broadcaster->getConfig());

        // customize config.
        $setReturn = $broadcaster->setConfig($config);

        // assert the fluent return.
        $this->assertSame($broadcaster, $setReturn);

        // now, assert the instance was correctly set and returned.
        $this->assertSame($config, $broadcaster->getConfig());
    }

    /**
     * Test access token getter and setter.
     */
    public function test_access_token_getter_and_setter()
    {
        // create a new token mock.
        $token = $this->mockToken();

        // creates a new broadcaster instance.
        $broadcaster = $this->getInstanceWithMocks();

        // assert the internal instance is not the same.
        $this->assertNotSame($token, $broadcaster->getToken());

        // customize token.
        $setReturn = $broadcaster->setToken($token);

        // assert the fluent return.
        $this->assertSame($broadcaster, $setReturn);

        // now, assert the instance was correctly set and returned.
        $this->assertSame($token, $broadcaster->getToken());
    }

    /**
     * Test http client getter and setter.
     */
    public function test_http_client_getter_and_setter()
    {
        // create a new http client mock.
        $httpClient = $this->mockHttpClient();

        // creates a new broadcaster instance.
        $broadcaster = $this->getInstanceWithMocks();

        // assert the internal instance is not the same.
        $this->assertNotSame($httpClient, $broadcaster->getHttpClient());

        // customize http client.
        $setReturn = $broadcaster->setHttpClient($httpClient);

        // assert the fluent return.
        $this->assertSame($broadcaster, $setReturn);

        // now, assert the instance was correctly set and returned.
        $this->assertSame($httpClient, $broadcaster->getHttpClient());
    }

    /**
     * Tests for the actual operation broadcasting.
     *
     * @throws
     */
    public function test_broadcasting()
    {
        // start a broadcaster mock.
        $broadcaster = $this->getInstanceWithMocks();

        // start a custom http client mock.
        $httpClient = $this->mockHttpClient();

        // create a mock http response.
        $mockHttpResponse = $this->mockHttpResponse();

        // when the http client receives a call, it should return the mock response.
        $httpClient->shouldReceive('call')->andReturn($mockHttpResponse);

        // customize the http client on the broadcaster.
        $broadcaster->setHttpClient($httpClient);

        // create a new, empty mock operation.
        $operation = $this->mockOperation();

        // broadcast the operation, and get the response/
        $response = $broadcaster->broadcast([$operation]);

        // assert the response is a SDK response instance.
        $this->assertInstanceOf(Response::class, $response);

        // the mock http response should be the same as the one previously
        // created for the mock http client.
        $this->assertSame($mockHttpResponse, $response->getHttpResponse());
    }

    /**
     * Test for exceptions thrown by the broadcast method.
     *
     * @throws
     */
    public function test_broadcast_exception_handling()
    {
        // start a broadcaster mock.
        $broadcaster = $this->getInstanceWithMocks();

        // start a custom http client mock.
        $httpClient = $this->mockHttpClient();

        // create a mock http response (error).
        $mockHttpResponse = $this->mockErrorHttpResponse();

        // when the http client receives a call, it should return the mock response.
        $httpClient->shouldReceive('call')->andReturnUsing(function () use ($mockHttpResponse) {
            // generate a mock for the request.
            /** @var RequestInterface $mockRequest */
            $mockRequest = Mockery::mock(RequestInterface::class);

            // throw a bad response exception.
            throw new BadResponseException('error', $mockRequest, $mockHttpResponse);
        });

        // customize the http client on the broadcaster.
        $broadcaster->setHttpClient($httpClient);

        // create a new, empty mock operation.
        $operation = $this->mockOperation();

        try {
            // broadcast the operation, that will throw an exception (bad response).
            $broadcaster->broadcast([$operation]);
        } catch (\Exception $e) {
            // assert that the response is actually, a SDK response exception.
            $this->assertInstanceOf(ResponseException::class, $e);
            /** @var $e ResponseException */
            $this->assertSame($e->getHttpResponse(), $mockHttpResponse);
        }

        // now, let's try for generic errors.
        // start a custom http client mock.
        $httpClient = $this->mockHttpClient();

        // when the call method gets called, it will throw a generic guzzle exception.
        $httpClient->shouldReceive('call')->andReturnUsing(function () {
            // throw a bad response exception.
            throw Mockery::mock(GuzzleException::class, \Exception::class);
        });

        // set the custom http client.
        $broadcaster->setHttpClient($httpClient);

        try {
            // broadcast the operation, that will throw an exception (generic guzzle exception).
            $broadcaster->broadcast([$operation]);
        } catch (\Exception $e) {
            // assert it's a client (SDK) generic exception.
            $this->assertInstanceOf(ClientException::class, $e);
        }
    }
}