<?php

namespace SteemConnect\Http;

use GuzzleHttp\Psr7\Request;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use GuzzleHttp\Client as HttpClient;
use SteemConnect\Operations\Comment;

/**
 * Class Client
 *
 * Http client that will make and parse requests to the SteemConnect API.
 */
class Client
{
    /**
     * @var Config|null Configuration instance.
     */
    protected $config = null;

    /**
     * @var HttpClient|null Guzzle http client instance.
     */
    protected $httpClient = null;

    /**
     * @var Token|null Access Token that will be used to authenticate requests.
     */
    protected $accessToken = null;

    /**
     * Client constructor.
     * @param Config|null $config
     * @param Token|null $token
     * @param HttpClient|null $httpClient
     */
    public function __construct(Config $config = null, Token $token = null, HttpClient $httpClient = null)
    {
        // config instance.
        $this->config = $config;

        // access token.
        $this->accessToken = $token;

        // get the http client (default one at constructor time.)
        $this->httpClient = $httpClient ? $httpClient : $this->getHttpClient();
    }

    /**
     * Replaces the configuration object.
     *
     * @param Config $config Config instance to replace on the client.
     *
     * @return $this
     */
    public function setConfig(Config $config) : self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Retrieves the current configuration object.
     *
     * @return Config Configuration instance.
     */
    public function getConfig() : ?Config
    {
        return $this->config;
    }

    /**
     * Custom Access Token setter.
     *
     * @param Token $token
     *
     * @return Client
     */
    public function setAccessToken(Token $token) : self
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Access Token getter.
     *
     * @return null|Token
     */
    public function getAccessToken() : ?Token
    {
        return $this->accessToken;
    }

    /**
     * HttpClient instance.
     *
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Returns the custom or factories a new HttpClient for the API client.
     *
     * @return HttpClient
     */
    public function getHttpClient() : HttpClient
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }

        return new HttpClient();
    }

    protected function defaultHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->accessToken->getToken()}",
        ];
    }

    public function call(string $method, string $uri, array $body = null)
    {
        $request = new Request($method, $this->config->buildUrl($uri), $this->defaultHeaders(), $body ? json_encode($body) : null);

        $response = $this->httpClient->send($request);

        return $response;
    }
}