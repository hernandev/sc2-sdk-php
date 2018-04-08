<?php

namespace SteemConnect\Client;

use GuzzleHttp\Exception\BadResponseException;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use GuzzleHttp\Exception\GuzzleException as HttpClientException;
use SteemConnect\Exceptions\ClientException;
use SteemConnect\Exceptions\ResponseException;
use SteemConnect\Http\Client as HttpClient;

/**
 * Class Broadcaster.
 *
 * Broadcaster handles the broadcasting and response parsing
 * or operations on SteemConnect calls.
 */
class Broadcaster
{
    /**
     * @var Config SDK configuration instance.
     */
    protected $config;

    /**
     * @var Token Account access token instance.
     */
    protected $token;

    /**
     * @var HttpClient HTTP client to use on requests on the SDK.
     */
    protected $httpClient;

    /**
     * Broadcaster constructor.
     *
     * @param Config $config
     * @param Token $token
     * @param HttpClient $httpClient
     */
    public function __construct(Config $config, Token $token, HttpClient $httpClient)
    {
        // assign config instance.
        $this->config = $config;

        // assign token instance.
        $this->token = $token;

        // assign http client instance.
        $this->httpClient = $httpClient;
    }

    /**
     * Get the current configuration instance on the broadcaster.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Customize the configuration on the broadcaster instance.
     *
     * @param Config $config
     *
     * @return self
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the current access token instance on the broadcaster.
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Customize the access token on the broadcaster.
     *
     * @param Token $token
     *
     * @return self
     */
    public function setToken(Token $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the current HttpClient instance on the broadcaster.
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Customize the HttpClient instance on the broadcaster.
     *
     * @param HttpClient $httpClient
     *
     * @return self
     */
    public function setHttpClient(HttpClient $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Broadcasts an Operation through SteemConnect V2.
     *
     * @param array $operations List of operations to broadcast, usually, just one.
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function broadcast($operations)
    {
        // collect the operations list to broadcast.
        $operationsList = collect($operations)->toArray();

        try {
            // call SteemConnect passing a list of operations to broadcast.
            $httpResponse = $this->getHttpClient()
                ->call('POST', 'api/broadcast', [
                    'operations' => $operationsList
                ]);

            // creates a client response instance.
            $response = new Response();
            // set the http response on the client response object.
            $response->setHttpResponse($httpResponse);

            // finally returns the response object.
            return $response;

        } catch (BadResponseException $e) {
            // throw a custom response exception otherwise.
            throw new ResponseException($e->getResponse());
        } catch (HttpClientException $e) {
            // throw a client exception, passing the previous one.
            throw new ClientException("Error broadcasting the operation", 0, $e);
        }
    }
}