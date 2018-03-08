<?php

namespace SteemConnect\Exceptions;

use Psr\Http\Message\ResponseInterface;
use SteemConnect\Http\HasHttpResponse;
use Throwable;

/**
 * Class ResponseException.
 *
 * HTTP Response related exceptions.
 */
class ResponseException extends ClientException
{
    // enable the response parsing trait.
    use HasHttpResponse;

    /**
     * ResponseException constructor.
     *
     * @param ResponseInterface $response
     * @param Throwable|null $previous
     */
    public function __construct(ResponseInterface $response, Throwable $previous = null)
    {
        // parses the http response.
        $this->setHttpResponse($response);

        // parse the message and status code, then call parent constructor.
        parent::__construct($this->parseResponseMessage(), $this->responseStatusCode, $previous);
    }

    /**
     * Parses the error response message into a single string for the exception.
     *
     * @return string
     */
    protected function parseResponseMessage() : string
    {
        // if the response is a json string.
        if ($this->responseIsJson()) {
            // collect the array body keys.
            $body = collect($this->responseBody);

            // parse the error name.
            $error = $body->get('error', null);
            // parse the error message, defaulting to HTTP.
            $message = $body->get('error_description', $this->responseStatusMessage);

            // return the formatted error message.
            return $error ? "{$error}: {$message}" : $message;
        }

        // in case the body is a string, just not an array / json.
        if (is_string($this->responseBody)) {
            return $this->responseBody;
        }

        // return the http status reason otherwise.
        return $this->responseStatusMessage;
    }
}