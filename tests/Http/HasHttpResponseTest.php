<?php

namespace SteemConnect\Http;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SteemConnect\TestCase;

/**
 * Class HasHttpResponseTest.
 *
 * Tests for the HasHttpResponse trait.
 */
class HasHttpResponseTest extends TestCase
{
    // enable the trait on the test itself.
    use HasHttpResponse;

    /**
     * Mock a Stream with JSON string contents.
     *
     * @return Mockery\MockInterface|StreamInterface
     */
    protected function makeJsonStreamMock()
    {
        // create a stream mock.
        $stream = Mockery::mock(StreamInterface::class);
        // return some JSON when serialized.
        $stream->shouldReceive('__toString')->andReturn('{"foo": "bar"}');

        // return the mock stream.
        return $stream;
    }

    /**
     * Mock a Stream with Text/HTML contents.
     *
     * @return Mockery\MockInterface|StreamInterface
     */
    protected function makeTextStreamMock()
    {
        // create a stream mock.
        $stream= Mockery::mock(StreamInterface::class);
        // return some html when serialized.
        $stream->shouldReceive('__toString')->andReturn('<div>foo - bar</div>');

        // return the mock stream.
        return $stream;
    }

    /**
     * Generic response mock factory.
     *
     * @param int $statusCode
     * @param string $reasonPhrase
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function makeResponseMock(int $statusCode = 200, string $reasonPhrase = 'Ok.')
    {
        // get a new mock response.
        $response = Mockery::mock(ResponseInterface::class);

        // set both status code and status reason (phrase) on the mock.
        $response->shouldReceive('getStatusCode')->andReturn($statusCode);
        $response->shouldReceive('getReasonPhrase')->andReturn($reasonPhrase);

        // return the mock response.
        return $response;
    }

    /**
     * Mock a normal (200) response.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function getNormalJsonResponse()
    {
        // start a mock with code 200.
        $response = $this->makeResponseMock(200, 'Ok');

        // the response headers should match a JSON response headers.
        $response->shouldReceive('getHeaders')->andReturn([
            'Content-Type' => 'application/json'
        ]);

        // and the body should return a Stream with JSON contents.
        $response->shouldReceive('getBody')->andReturn($this->makeJsonStreamMock());

        // return the mock response.
        return $response;
    }

    /**
     * Mock a normal (200) response.
     *
     * @return Mockery\MockInterface|ResponseInterface
     */
    protected function getNormalNonJsonResponse()
    {
        // start a mock with code 200.
        $response = $this->makeResponseMock(200, 'Ok');

        // instead of JSON, the headers should be anything else, in this case, HTML.
        $response->shouldReceive('getHeaders')->andReturn([
            'Content-Type' => 'text/html'
        ]);

        // the body stream should also return anything but JSON, in this case, some HTML tags.
        $response->shouldReceive('getBody')->andReturn($this->makeTextStreamMock());

        // return the mock response.
        return $response;
    }

    /**
     * Test JSON response parsing.
     */
    public function test_json_detection()
    {
        // set the HTTP response.
        $this->setHttpResponse($this->getNormalJsonResponse());
        // assert the response is JSON (JSON detection).
        $this->assertTrue($this->responseIsJson());
    }

    /**
     * Test NON-JSON (text/html and other) responses parsing.
     */
    public function test_text_detection()
    {
        // set a non-json response/
        $this->setHttpResponse($this->getNormalNonJsonResponse());

        // assert the response is not JSON.
        $this->assertFalse($this->responseIsJson());
    }

    /**
     * Tests for the internal http response getters and setters.
     */
    public function test_http_response_getter_and_setter()
    {
        // assert the initial response content is empty
        $this->assertNull($this->getHttpResponse());

        // get a response mock.
        $mockHttpResponse = $this->getNormalJsonResponse();

        // set the mock http response.
        $this->setHttpResponse($mockHttpResponse);

        // assert the set was ok, by comparing the local instance with getter instance.
        $this->assertSame($mockHttpResponse, $this->getHttpResponse());
    }
}