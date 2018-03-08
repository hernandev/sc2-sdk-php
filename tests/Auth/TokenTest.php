<?php

namespace SteemConnect\Auth;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use SteemConnect\Exceptions\TokenException;

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

    public function test_token_parsing()
    {
        $token = Token::fromArray($this->validToken);

        $this->assertInstanceOf(Token::class, $token);
    }

    public function test_empty_token_parsing_exception()
    {
        try {
            Token::fromArray([]);
        } catch (\Exception $e) {
            $this->assertInstanceOf(TokenException::class, $e);
        }
    }

    public function test_json_serialization()
    {
        $originalJson = json_encode($this->validToken);

        $token = Token::fromArray($this->validToken);

        $this->assertEquals($originalJson, json_encode($token->jsonSerialize()));

        $this->assertEquals($originalJson, json_encode($token->toArray()));
    }

    public function test_token_parsing_from_json_string()
    {
        $jsonString = json_encode($this->validToken);

        $token = Token::fromJsonString($jsonString);

        $this->assertInstanceOf(Token::class, $token);
    }

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