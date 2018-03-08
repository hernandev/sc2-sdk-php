<?php

namespace SteemConnect\Client;

use SteemConnect\Http\HasHttpResponse;
use SteemConnect\Transactions\Transaction;

/**
 * Class Response.
 *
 * Class responsible for wrapping the http responses into a transactions.
 */
class Response
{
    // enable the http response handler trait.
    use HasHttpResponse;

    /**
     * Retrieves the Http transaction.
     *
     * @return null|Transaction
     */
    public function getTransaction() : ?Transaction
    {
        // if no body was present, or, the body is not an array.
        if (!$this->responseBody || !is_array($this->responseBody)) {
            // then just return null cause there's no transaction to parse.
            return null;
        }

        // otherwise, factories a transaction from the response body itself and return.
        return Transaction::factory($this->responseBody);
    }
}