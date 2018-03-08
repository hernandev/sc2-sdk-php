<?php

namespace SteemConnect\Http;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Trait HasHttpResponse.
 *
 * Trait for classes which will carry http responses internally.
 */
trait HasHttpResponse
{
    /**
     * @var null|ResponseInterface Http response instance.
     */
    protected $responseInstance = null;

    /**
     * @var null|int Http response status code.
     */
    protected $responseStatusCode = null;

    /**
     * @var null|string Http response reason / http code description.
     */
    protected $responseStatusMessage = null;

    /**
     * @var null|string|array Http response body, parsed.
     */
    protected $responseBody = null;

    /**
     * @var null|Collection List of Http response headers.
     */
    protected $responseHeaders = null;

    /**
     * Configure the http response on the current resource.
     *
     * @param ResponseInterface $response
     */
    public function setHttpResponse(ResponseInterface $response)
    {
        // assign the http response instance itself.
        $this->responseInstance = $response;

        // assign the http response status code.
        $this->responseStatusCode = $response->getStatusCode();

        // assign the http response status message.
        $this->responseStatusMessage = $response->getReasonPhrase();

        // assign the http response headers.
        $this->responseHeaders = collect($response->getHeaders());

        // assign the parsed http response body.
        $this->responseBody = $this->decodeResponseBody($response->getBody());

    }

    /**
     * Decode the response body, parsing to array when json.
     *
     * @param StreamInterface $body
     *
     * @return mixed|string
     */
    protected function decodeResponseBody(StreamInterface $body)
    {
        if ($this->responseIsJson()) {
            return json_decode((string) $body, true);
        } else {
            return (string) $body;
        }
    }

    /**
     * Detect the response as being a json response or not.
     *
     * @return bool
     */
    protected function responseIsJson() : bool
    {
        $type = json_encode($this->responseHeaders->get('Content-Type'));

        return Str::contains($type, 'json');
    }
}