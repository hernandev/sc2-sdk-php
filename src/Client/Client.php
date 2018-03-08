<?php

namespace SteemConnect\Client;

use SteemConnect\Auth\Manager;
use SteemConnect\Auth\Token;
use SteemConnect\Config\Config;
use SteemConnect\Exceptions\ClientException;
use SteemConnect\OAuth2\Provider\Provider;
use SteemConnect\Http\Client as HttpClient;

/**
 * Class Client.
 *
 * SteemConnect V2 Client.
 *
 * This is the main wrapper/manager class that will be use to actually call SteemConnect
 * to broadcast operations and authenticate users.
 */
class Client
{
    /**
     * @var Config All configurations for SC2 SDK client instance.
     */
    protected $config;

    /**
     * @var null|Token Access Token instance, set from callback parsing or already existing token.
     */
    protected $token = null;

    /**
     * @var null|Provider SC2 OAuth client/provider.
     */
    protected $provider = null;

    /**
     * @var null|HttpClient
     */
    protected $httpClient = null;

    /**
     * Client constructor.
     *
     * @param Config $config Configuration instance.
     */
    public function __construct(Config $config)
    {
        // setup authentication instance.
        $this->setConfig($config);

        // setup provider instance.
        $this->setOAuthProvider($this->createProvider());

        // setup http client instance.
         $this->setHttpClient($this->createHttpClient());
    }

    /**
     * Set an already existing token into client instance.
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
     * Returns the instance token.
     *
     * @return Token Access token instance, if any.
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * Override the configuration instance.
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
     * Retrieves the current configuration instance set on the client.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Override the http client instance on the SDK.
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
     * Retrieves the current Http client configured on the SDK.
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Refresh the HttpClient instance.
     *
     * This method is intended for cases where configuration is replaced after client instance has
     * been created.
     *
     * @return self
     */
    public function refreshHttpClient(): self
    {
        $this->setHttpClient($this->createHttpClient());

        return $this;
    }

    /**
     * Set a custom OAuth2 provider instance.
     *
     * Most useful for testing and extending the provider than actual day-to-day usage.
     *
     * @param Provider $provider
     *
     * @return self
     */
    public function setOAuthProvider(Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Returns the current OAuth2 provider instance set on the client.
     *
     * @return null|Provider
     */
    public function getOAuthProvider(): ?Provider
    {
        return $this->provider;
    }

    /**
     * Refresh the provider instance.
     *
     * This method is intended for cases where configuration is replaced after client instance has
     * been created.
     *
     * @return self
     */
    public function refreshProvider(): self
    {
        $this->setOAuthProvider($this->createProvider());

        return $this;
    }

    /**
     * Proxy for the authentication manager instance.
     *
     * @throws ClientException
     *
     * @return Manager
     */
    public function auth()
    {
        try {
            // creates a fresh provider instance to reflect configuration data.
            $this->refreshProvider();

            // returns a new manager, if possible.
            return new Manager($this->config, $this->provider, $this->token);
        } catch (\Exception $e) {
            // throw the custom exception about authentication instance not being created correctly.
            throw new ClientException('Error preparing authentication manager, ensure the configuration is correct.');
        }

    }

    /**
     * @param array ...$operations
     *
     * @return Response
     */
    public function broadcast(...$operations)
    {
        // refresh the oauth provider before doing anything.
        $this->refreshProvider();
        // refresh the http client because the instances may have changed.
        $this->refreshHttpClient();

        // returns the broadcast operation result.
        // notice no try catch here because the broadcast will throw
        // a response or request exception internally and it should just
        // be forwarded.
        return $this->createBroadcaster()->broadcast($operations);
    }

    /**
     * Creates a new OAuth2 provider instance.
     *
     * @return Provider
     */
    protected function createProvider()
    {
        return new Provider($this->config);
    }

    /**
     * Creates a new HttpClient instance.
     *
     * @return HttpClient
     */
    protected function createHttpClient()
    {
        return new HttpClient($this->getConfig(), $this->getToken());

    }

    /**
     * Creates a broadcaster instance.
     *
     * @return Broadcaster
     */
    protected function createBroadcaster()
    {
        return new Broadcaster($this->config, $this->token, $this->httpClient);
    }
}