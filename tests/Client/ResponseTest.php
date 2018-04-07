<?php

namespace SteemConnect\Client;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SteemConnect\TestCase;
use SteemConnect\Transactions\Transaction;

/**
 * Class ResponseTest.
 *
 * Tests for the client Response class.
 */
class ResponseTest extends TestCase
{
    /**
     * @var bool|string Stub transaction data as JSON string.
     */
    protected $transactionJson;

    /**
     * @var array|mixed Stub transaction data as array.
     */
    protected $transactionData = [];

    /**
     * ResponseTest constructor.
     *
     * @param null|string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        // call parent constructor.
        parent::__construct($name, $data, $dataName);

        // get the stub transaction json content.
        $this->transactionJson = file_get_contents(__DIR__.'/../Resources/stub-transaction.json');

        // decode the json stub transaction into array.
        $this->transactionData = json_decode($this->transactionJson, true);
    }

    /**
     * Mock a valid transaction http response.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function mockHttpResponse()
    {
        // mock a response.
        $response = Mockery::mock(ResponseInterface::class);

        // status codes and reason phrase.
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getReasonPhrase')->andReturn('OK');

        // json headers.
        $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => 'application/json']);

        // transaction json body.
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn($this->transactionJson);

        // response body stream.
        $response->shouldReceive('getBody')->andReturn($body);

        // response return.
        return $response;
    }

    /**
     * Mock a invalid, empty http response.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function mockEmptyHttpResponse()
    {
        // mock a response.
        $response = Mockery::mock(ResponseInterface::class);

        // status codes and reason phrase.
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getReasonPhrase')->andReturn('OK');

        // json headers.
        $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => 'application/json']);

        // transaction json body.
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn("");

        // response body stream.
        $response->shouldReceive('getBody')->andReturn($body);

        // response return.
        return $response;
    }

    /**
     * Parse the transaction from the response.
     */
    public function test_parsing_into_transaction()
    {
        // get the mock http response.
        $httpResponse = $this->mockHttpResponse();

        // start a new response instance.
        $response = new Response();

        // set the http response on the response instance.
        $response->setHttpResponse($httpResponse);

        // get the transaction from the http response.
        $transaction = $response->getTransaction();

        // assert the transaction instance was returned.
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    /**
     * Test invalid responses.
     */
    public function test_parsing_invalid_responses()
    {
        // get the mock http response.
        $httpResponse = $this->mockEmptyHttpResponse();

        // start a new response instance.
        $response = new Response();

        // set the http response on the response instance.
        $response->setHttpResponse($httpResponse);

        // get the transaction from the http response.
        // this return should be null.
        $transaction = $response->getTransaction();

        // assert the return was null.
        $this->assertNull($transaction);
    }
}