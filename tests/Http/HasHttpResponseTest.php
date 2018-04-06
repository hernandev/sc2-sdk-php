<?php

namespace SteemConnect\Http;

use GuzzleHttp\Psr7\Stream;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SteemConnect\TestCase;

class HasHttpResponseTest extends TestCase
{
    use HasHttpResponse;

    protected function makeJsonStreamMock()
    {
        $stream= Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('__toString')->andReturn('{"foo": "bar"}');

        return $stream;
    }

    protected function makeTextStreamMock()
    {
        $stream= Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('__toString')->andReturn('<div>foo - bar</div>');

        return $stream;
    }

    protected function makeResponseMock(int $statusCode = 200, string $reasonPhrase = 'Ok.')
    {
        $response = Mockery::mock(ResponseInterface::class);

        $response->shouldReceive('getStatusCode')->andReturn($statusCode);
        $response->shouldReceive('getReasonPhrase')->andReturn($reasonPhrase);

        return $response;
    }

    /**
     * @return ResponseInterface
     */
    protected function getNormalJsonResponse()
    {
        $response = $this->makeResponseMock(200, 'Ok');
        $response->shouldReceive('getHeaders')->andReturn([
            'Content-Type' => 'application/json'
        ]);

        $response->shouldReceive('getBody')->andReturn($this->makeJsonStreamMock());

        return $response;
    }

    /**
     * @return ResponseInterface
     */
    protected function getNormalNonJsonResponse()
    {
        $response = $this->makeResponseMock(200, 'Ok');
        $response->shouldReceive('getHeaders')->andReturn([
            'Content-Type' => 'text/html'
        ]);

        $response->shouldReceive('getBody')->andReturn($this->makeTextStreamMock());

        return $response;
    }

    public function test_json_parsing()
    {
        $this->setHttpResponse($this->getNormalJsonResponse());
        $this->assertTrue($this->responseIsJson());
    }

    public function test_text_parsing()
    {
        $this->setHttpResponse($this->getNormalNonJsonResponse());
        $this->assertFalse($this->responseIsJson());
    }
}