<?php

namespace SteemConnect\Auth;

use Carbon\Carbon;
use SteemConnect\Exceptions\TokenException;
use SteemConnect\TestCase;

/**
 * Class TokenTest.
 *
 * Access token tests.
 */
class TokenTest extends TestCase
{
    /**
     * @var array Valid format token for testing.
     */
    protected $validToken = [];

    /**
     * Setup method.
     */
    public function setUp()
    {
        parent::setUp();

        $this->validToken = [
            'username' => 'foo-username',
            'access_token' => 'jwt.dummy.token',
            'refresh_token' => 'jwt.dummy.refresh',
            'expires' => Carbon::now()->addDays(1)->timestamp,
            'resource_owner_id' => 'foo-username',
        ];
    }

    /**
     * Token parsing.
     */
    public function test_token_parsing()
    {
        $token = Token::fromArray($this->validToken);

        $this->assertInstanceOf(Token::class, $token);
    }

    /**
     * Empty token.
     */
    public function test_empty_token_parsing_exception()
    {
        try {
            Token::fromArray([]);
        } catch (\Exception $e) {
            $this->assertInstanceOf(TokenException::class, $e);
        }
    }

    /**
     * Token JSON serialization tests.
     */
    public function test_json_serialization()
    {
        $originalJson = json_encode($this->validToken);

        $token = Token::fromArray($this->validToken);

        $this->assertEquals($originalJson, json_encode($token->jsonSerialize()));

        $this->assertEquals($originalJson, json_encode($token->toArray()));
    }

    /**
     * Token JSON string parsing tests.
     */
    public function test_token_parsing_from_json_string()
    {
        $jsonString = json_encode($this->validToken);

        $token = Token::fromJsonString($jsonString);

        $this->assertInstanceOf(Token::class, $token);
    }

    /**
     * Invalid token parsing tests.
     */
    public function test_token_parsing_from_json_string_with_invalid_data()
    {
        $jsonString = json_encode(['foo' => 'bar']);

        try {
            $token = Token::fromJsonString($jsonString);
        } catch (\Exception $e) {
            $this->assertInstanceOf(TokenException::class, $e);
        }

        $jsonString = '{';

        try {
            $token = Token::fromJsonString($jsonString);
        } catch (\Exception $e) {
            $this->assertInstanceOf(TokenException::class, $e);
        }
    }
}