<?php

namespace SteemConnect\Auth;

use Illuminate\Contracts\Support\Arrayable;
use League\OAuth2\Client\Token\AccessToken;
use SteemConnect\Exceptions\TokenException;

/**
 * Class Token.
 *
 * Access token representation.
 *
 * This class is a custom token representation and should be used for all token operations.
 */
class Token extends AccessToken implements Arrayable
{
    /**
     * Factories a new Token instance from a JSON string (useful for storing it's data as JSON).
     *
     * @param string $tokenJsonString
     *
     * @throws TokenException
     *
     * @return Token
     */
    public static function fromJsonString(string $tokenJsonString = "{}") : Token
    {
        $data = json_decode($tokenJsonString, true);

        if (!$data) {
            throw new TokenException('Token JSON data could not be un-serialized.');
        }

        try {
            return self::fromArray($data);
        } catch (TokenException $e) {
            throw $e;
        }
    }

    /**
     * Parses a Token from array.
     *
     * @param array $tokenData Token data array to parse a new instance.
     *
     * @throws TokenException Exception error for invalid data.
     *
     * @return Token Access Token instance.
     */
    public static function fromArray(array $tokenData = []) : Token
    {
        try {
            return new self($tokenData);
        } catch (\Exception $e) {
            throw new TokenException("Invalid token data.");
        }
    }

    /**
     * Array serialization of token.
     *
     * @return array|mixed
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }
}