<?php

namespace SteemConnect\Auth;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Container\Container;
use SteemConnect\Config\Config;
use SteemConnect\Exceptions\TokenException;
use SteemConnect\Http\Client;
use SteemConnect\Http\Response;
use SteemConnect\OAuth2\Provider\Provider;
use SteemConnect\Operations\Comment;
use SteemConnect\Operations\Operation;
use SteemConnect\Operations\Vote;

/**
 * Class Manager.
 *
 * Authentication manager wraps all authentication and token management related logic. Including
 * calls to the OAuth2 client.
 */
class Manager
{
    /**
     * @var Config Configuration object instance.
     */
    protected $config;

    /**
     * @var Provider OAuth2 client instance.
     */
    protected $provider;

    /**
     * @var Token|null Access Token instance.
     */
    protected $accessToken = null;

    /**
     * @var array Map Steem operation types into classes representing them.
     */
    protected $operations = [
        // votes.
        'vote' => Vote::class,
        // comment.
        'comment' => Comment::class,
    ];

    /**
     * Manager constructor.
     *
     * @param Config $config Configuration object instance.
     * @param Provider $provider OAuth2 provider instance.
     * @param Token|null Already existing access token instance.
     */
    public function __construct(Config $config, Provider $provider, Token $token = null)
    {
        // config instance.
        $this->config = $config;

        // setup provider.
        $this->provider = $provider;

        // set the access token on the manager instance.
        $this->accessToken = $token;
    }

    /**
     * Returns the provider authorization URL.
     *
     * @param array $options Additional options for the URL building.
     *
     * @return string
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        return $this->provider->getAuthorizationUrl($options);
    }

    /**
     * Parses the OAuth2 callback/return code and exchange for an access token.
     *
     * @param string|null $code
     *
     * @return Token
     */
    public function parseReturn(string $code = null)
    {
        try {
            // get the original token object.
            $oAuthToken = $this->provider->parseReturn($code);
        } catch (\Exception $e) {
            // throw token exception if return parsing could not be successful.
            throw new TokenException('Error while exchanging access code with access token.');
        }


        // return a new SDK token object.
        $this->accessToken = new Token($oAuthToken->jsonSerialize());

        // return the access token parsed from response.
        return $this->accessToken;
    }

    /**
     * Factories a new Http Client from inside the manager.
     *
     * @return Client
     */
    protected function getHttpClient()
    {
        // setup a new HttpClient.
        return new Client($this->config, $this->accessToken);
    }

    protected function postBroadcast(array $operationsList = [])
    {
        try {
            $response = $this->getHttpClient()->call('POST', 'api/broadcast', [ 'operations' => $operationsList ]);

            return new Response($response);
        } catch (BadResponseException $e) {
            return new Response($e->getResponse());
        }
    }

    /**
     * Broadcasts an Operation through SteemConnect V2.
     *
     * @param array $operations List of operations to broadcast, usually, just one.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function broadcast(...$operations)
    {
        $operationsList = collect($operations)->toArray();

        try {
            $response = $this->getHttpClient()->call('POST', 'api/broadcast', [ 'operations' => $operationsList ]);

            return new Response($response);
        } catch (BadResponseException $e) {
            return new Response($e->getResponse());
        }
    }
}