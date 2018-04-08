<?php

namespace SteemConnect\Transactions;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SteemConnect\TestCase;

/**
 * Class TransactionTest.
 *
 * Tests for the Transaction class.
 */
class TransactionTest extends TestCase
{
    /**
     * @var bool|string Stub transaction data as JSON string.
     */
    protected $transactionJson;

    /**
     * @var array|mixed Stub transaction data as array.
     */
    protected $transactionData = [];

    /**
     * @var Transaction Instance of transaction for testing.
     */
    protected $transaction;

    /**
     * ResponseTest constructor.
     *
     * @param null|string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        // call parent constructor.
        parent::__construct($name, $data, $dataName);

        // get the stub transaction json content.
        $this->transactionJson = file_get_contents(__DIR__.'/../Resources/stub-transaction.json');

        // decode the json stub transaction into array.
        $this->transactionData = json_decode($this->transactionJson, true);

        // make sure the transaction data is sorted by key.
        ksort($this->transactionData);

        // factory the transaction instance from the data.
        $this->transaction = Transaction::factory($this->transactionData);
    }

    /**
     * Test the transaction data parsing and it's serialization.
     */
    public function test_transaction_getters()
    {
        // start a transaction instance with the stub data.
        $transaction = Transaction::factory($this->transactionData);

        // test transaction id.
        $this->assertEquals($this->transactionData['id'], $transaction->getId());

        // test block number.
        $this->assertEquals($this->transactionData['block_num'], $transaction->getBlockNumber());

        // test transaction number.
        $this->assertEquals($this->transactionData['trx_num'], $transaction->getTransactionNumber());

        // test expiration status.
        $this->assertEquals($this->transactionData['expired'], $transaction->getExpired());

        // ensure the expiration is a carbon instance.
        $this->assertInstanceOf(Carbon::class, $transaction->getExpiration());

        // format the expiration for testing.
        $formattedExpiration = $transaction->getExpiration()->format('Y-m-d\TH:i:s');
        // test if the expiration matches (after formatting).
        $this->assertEquals($this->transactionData['expiration'], $formattedExpiration);

        // test reference block number.
        $this->assertEquals($this->transactionData['ref_block_num'], $transaction->getReferenceBlockNumber());

        // test reference block prefix.
        $this->assertEquals($this->transactionData['ref_block_prefix'], $transaction->getReferenceBlockPrefix());

        // test the operations are a collection.
        $this->assertInstanceOf(Collection::class, $transaction->getOperations());
        // test the operations (as array).
        $this->assertEquals($this->transactionData['operations'], $transaction->getOperations()->toArray());

        // test signatures are a collection.
        $this->assertInstanceOf(Collection::class, $transaction->getSignatures());
        // test the signatures (as array).
        $this->assertEquals($this->transactionData['signatures'], $transaction->getSignatures()->toArray());

        // test extensions are a collection.
        $this->assertInstanceOf(Collection::class, $transaction->getExtensions());
        // test the extensions (as array).
        $this->assertEquals($this->transactionData['extensions'], $transaction->getExtensions()->toArray());

        // parse the transaction back to json.
        $parsedJson = json_encode($transaction->toArray());
        $originalJson = json_encode([ 'result' => $this->transactionData ]);

        // assert the transaction have the same content after parsed.
        $this->assertEquals($parsedJson, $originalJson);
    }

    /**
     * Test parsing of transaction when errors happens.
     */
    public function test_parsing_errors_no_matching_name()
    {
        // copy the transaction data to a local array.
        $data = $this->transactionData;

        // rename the operation to a custom name.
        $data['operations'][0][0] = 'some-other-name';

        // factory the transaction.
        $transaction = Transaction::factory($data);

        // try getting the operations.
        $this->assertInstanceOf(Collection::class, $transaction->getOperations());
    }

    /**
     * Test parsing of transaction when errors happens.
     */
    public function test_parsing_errors_no_name_present()
    {
        // copy the transaction data to a local array.
        $data = $this->transactionData;

        // rename the operation to a custom name.
        $data['operations'][0][0] = null;

        // factory the transaction.
        $transaction = Transaction::factory($data);

        // try getting the operations.
        $this->assertInstanceOf(Collection::class, $transaction->getOperations());
    }
}