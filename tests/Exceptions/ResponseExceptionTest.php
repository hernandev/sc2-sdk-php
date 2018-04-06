<?php

namespace SteemConnect\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SteemConnect\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseExceptionTest.
 *
 * Test the customized response exception class.
 */
class ResponseExceptionTest extends TestCase
{
    /**
     * @var int HTTP code for the error response.
     */
    protected $statusCode = 401;

    /**
     * @var string HTTP reason phrase for the status on the error response.
     */
    protected $reasonPhrase = 'Unauthorized';

    /**
     * @var string Com error message to include on the tests.
     */
    protected $responseErrorMessage = 'THIS-IS-AN-ERROR';

    /**
     * @var string HTML version of a generic error.
     */
    protected $responseErrorMessageHtml = '<html><body>foo</body></html>';

    /**
     * Generates a mock response for testing the exception customized logic.
     *
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param string $contentType
     *
     * @return ResponseInterface
     */
    protected function makeResponseMock(int $statusCode, string $reasonPhrase, string $contentType)
    {
        // start a response mock.
        $response = Mockery::mock(ResponseInterface::class);

        // set the status code and reason phrase returns.
        $response->shouldReceive('getStatusCode')->andReturn($statusCode);
        $response->shouldReceive('getReasonPhrase')->andReturn($reasonPhrase);
        // set content type on the response headers using the desired value from parameter.
        $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => $contentType]);

        // alias the variable to make it nice on the IDE>
        /** @var ResponseInterface $response */
        return $response;
    }

    /**
     * Generates a mock of a body stream containing an error.
     *
     * @return StreamInterface
     */
    protected function makeErrorStreamMock()
    {
        // mock a stream with error.
        $stream = Mockery::mock(StreamInterface::class);

        // error response body.
        $errorResponseBody = [
            'error' => $this->responseErrorMessage,
        ];

        // set the string serialization of the stream.
        $stream->shouldReceive('__toString')->andReturn(json_encode($errorResponseBody));

        /** @var StreamInterface $stream */
        return $stream;
    }

    /**
     * Generates a mock of a body stream containing an error.
     *
     * This time, HTML version.
     *
     * @return StreamInterface
     */
    protected function makeHtmlErrorStreamMock()
    {
        // mock a stream with error.
        $stream = Mockery::mock(StreamInterface::class);

        // set the string serialization of the stream.
        $stream->shouldReceive('__toString')->andReturn($this->responseErrorMessageHtml);

        /** @var StreamInterface $stream */
        return $stream;
    }

    /**
     * Generates a mock HTTP error response.
     *
     * @return ResponseInterface
     */
    protected function mockErrorResponse()
    {
        // generate a mock response, using the code and reason on the instance.
        /** @var Mockery\MockInterface $response */
        $response = $this->makeResponseMock($this->statusCode, $this->reasonPhrase, 'application/json');

        // assign the error stream on the response body before returning the response mock.
        $response->shouldReceive('getBody')->andReturn($this->makeErrorStreamMock());

        /** @var ResponseInterface $response */
        return $response;
    }

    /**
     * Generates a mock HTTP error response.
     *
     * This time, the mock response is not a json error but a generic html one.
     *
     * @return ResponseInterface
     */
    protected function mockHtmlErrorResponse()
    {
        // generate a mock response, using the code and reason on the instance.
        /** @var Mockery\MockInterface $response */
        $response = $this->makeResponseMock($this->statusCode, $this->reasonPhrase, 'text/html');

        // assign the error stream on the response body before returning the response mock.
        $response->shouldReceive('getBody')->andReturn($this->makeHtmlErrorStreamMock());

        /** @var ResponseInterface $response */
        return $response;
    }

    /**
     * Test the exception construction and parsing
     */
    public function test_exception_construction_and_error_parsing()
    {
        // generate the exception with the mock error response.
        $exception = new ResponseException($this->mockErrorResponse());

        // assure it inherits RuntimeException.
        $this->assertInstanceOf(\RuntimeException::class, $exception);

        // assert the message on the exception was extracted correctly.
        $this->assertEquals($exception->getMessage(), "{$this->responseErrorMessage}: $this->reasonPhrase");
    }

    /**
     * Test the exception construction and parsing for non JSON responses.
     */
    public function test_exception_construction_and_error_parsing_on_non_json_responses()
    {
        // generate the exception with the mock error response.
        $exception = new ResponseException($this->mockHtmlErrorResponse());

        // assure it inherits RuntimeException.
        $this->assertInstanceOf(\RuntimeException::class, $exception);

        // assert the message on the exception was extracted correctly.
        // the html errors will strip all html tags.
        $this->assertEquals($exception->getMessage(), "foo");
    }
}