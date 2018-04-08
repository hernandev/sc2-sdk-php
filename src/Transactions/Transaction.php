<?php

namespace SteemConnect\Transactions;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use SteemConnect\Operations\Comment;
use SteemConnect\Operations\CustomJson;
use SteemConnect\Operations\Operation;
use SteemConnect\Operations\Vote;

/**
 * Class Transaction.
 *
 * This class represents a transaction on the Steem Blockchain.
 *
 * SteemConnect responses, in cases of success are the raw transaction where
 * the operations being broadcast were included.
 */
class Transaction implements Arrayable
{
    /**
     * @var null|string STEEM transaction ID.
     */
    protected $id;

    /**
     * @var null|int STEEM block number (block which the transaction were included).
     */
    protected $blockNumber;

    /**
     * @var null|int Block transaction number. (a small integer corresponding the position on the block).
     */
    protected $transactionNumber;

    /**
     * @var null|bool Transaction expiration status.
     */
    protected $expired = false;

    /**
     * @var null|int Number of an older STEEM block being referenced on the current block (hard-fork mechanism).
     */
    protected $referenceBlockNumber;

    /**
     * @var null|int Prefix of an older STEEM block being referenced (hard-fork mechanism).
     */
    protected $referenceBlockPrefix;

    /**
     * @var null|\Carbon\Carbon Expiration date.
     */
    protected $expiration;

    /**
     * @var \Illuminate\Support\Collection Operations included on the transaction.
     */
    protected $operations;

    /**
     * @var \Illuminate\Support\Collection Operation extensions included on the transaction.
     */
    protected $extensions;

    /**
     * @var \Illuminate\Support\Collection List of signatures for the transaction.
     */
    protected $signatures;

    /**
     * @var array List of operation names and they respective representing classes.
     */
    protected $operationClassMap = [
        'vote' => Vote::class,
        'comment' => Comment::class,
        'custom_json' => CustomJson::class,
    ];

    /**
     * Transaction constructor.
     *
     * @param array $transactionData
     */
    public function __construct(array $transactionData = [])
    {
        // init operations as an empty collection.
        $this->operations = collect([]);
        // init extensions as an empty collection.
        $this->extensions = collect([]);
        // init signatures as an empty collection.
        $this->signatures = collect([]);
    }

    /**
     * Factors a transaction data into a transaction instance.
     *
     * @param array $transactionData
     *
     * @return self
     */
    public static function factory(array $transactionData = [])
    {
        // extract the result key from transaction data.
        $data = array_has($transactionData, 'result') ? array_get($transactionData, 'result') : $transactionData;

        // create a transaction instance.
        $transaction = new self();

        // set transaction id.
        $transaction->setId(array_get($data, 'id', null));
        // set block number.
        $transaction->setBlockNumber(array_get($data, 'block_num', null));
        // set transaction position on block.
        $transaction->setTransactionNumber(array_get($data, 'trx_num', null));
        // set transaction expiration status.
        $transaction->setExpired(array_get($data, 'expired', null));
        // set reference block number.
        $transaction->setReferenceBlockNumber(array_get($data, 'ref_block_num', null));
        // set reference block prefix.
        $transaction->setReferenceBlockPrefix(array_get($data, 'ref_block_prefix', null));
        // set expiration date.
        $transaction->setExpiration(array_get($data, 'expiration', null));
        // set transaction operations.
        $transaction->setOperations(collect(array_get($data, 'operations', [])));
        // set transaction extensions.
        $transaction->setExtensions(collect(array_get($data, 'extensions', [])));
        // set transaction signatures.
        $transaction->setSignatures(collect(array_get($data, 'signatures', [])));

        // returns the transaction object.
        return $transaction;
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     * 
     * @return self
     */
    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBlockNumber(): ?int
    {
        return $this->blockNumber;
    }

    /**
     * @param int|null $blockNumber
     *
     * @return self
     */
    public function setBlockNumber(?int $blockNumber): self
    {
        $this->blockNumber = $blockNumber;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTransactionNumber(): ?int
    {
        return $this->transactionNumber;
    }

    /**
     * @param int|null $transactionNumber
     *
     * @return self
     */
    public function setTransactionNumber(?int $transactionNumber): self
    {
        $this->transactionNumber = $transactionNumber;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getExpired(): ?bool
    {
        return $this->expired;
    }

    /**
     * @param bool|null $expired
     *
     * @return self
     */
    public function setExpired(?bool $expired): self
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getReferenceBlockNumber(): ?int
    {
        return $this->referenceBlockNumber;
    }

    /**
     * @param int|null $referenceBlockNumber
     *
     * @return self
     */
    public function setReferenceBlockNumber(?int $referenceBlockNumber): self
    {
        $this->referenceBlockNumber = $referenceBlockNumber;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getReferenceBlockPrefix(): ?int
    {
        return $this->referenceBlockPrefix;
    }

    /**
     * @param int|null $referenceBlockPrefix
     *
     * @return self
     */
    public function setReferenceBlockPrefix(?int $referenceBlockPrefix): self
    {
        $this->referenceBlockPrefix = $referenceBlockPrefix;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getExpiration(): ?Carbon
    {
        return $this->expiration;
    }

    /**
     * @param string|Carbon|null $expiration
     *
     * @return self
     */
    public function setExpiration($expiration): self
    {
        // carbon is smart enough to parse both strings and carbon instances.
        $this->expiration = $expiration ? Carbon::parse($expiration, 'UTC') : null;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    /**
     * @param Collection $operations
     *
     * @return self
     */
    public function setOperations(Collection $operations): self
    {
        // parse the operations.
        $parsedOperations = $this->parseOperations($operations);

        // map the operations.
        $parsedOperations->map(function (Operation $operation) {
            $this->operations->push($operation);
        });

        // fluent return.
        return $this;
    }

    /**
     * @return Collection
     */
    public function getExtensions(): Collection
    {
        return $this->extensions;
    }

    /**
     * @param Collection $extensions
     *
     * @return self
     */
    public function setExtensions(Collection $extensions): self
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSignatures(): Collection
    {
        return $this->signatures;
    }

    /**
     * @param Collection $signatures
     *
     * @return self
     */
    public function setSignatures(Collection $signatures): self
    {
        $this->signatures = $signatures;

        return $this;
    }


    /**
     * This method parses a collection of operations as array into
     * they respective operation object instances, meaning working
     * with the objects should be a canonical representation
     * which can be serialized and de-serialized as needed.
     *
     * @param Collection $operationList
     *
     * @return Collection
     */
    protected function parseOperations(Collection $operationList) : Collection
    {
        $operations = $operationList->map(function (array $operationData) {
            $operationName = array_get($operationData, 0, null);

            if (!$operationName) {
                return null;
            }

            $operationClass = array_get($this->operationClassMap, $operationName, null);

            if (!$operationClass || !class_exists($operationClass)) {
                return null;
            }

            return new $operationClass(array_get($operationData, 1));
        });

        return $operations->filter();
    }


    /**
     * Array representation of a transaction.
     *
     * This method should return a transaction array that when converted to json,
     * reflects the original transaction json from response, meaning it will still
     * be valid for signature verifications.
     *
     * @return array
     */
    public function toArray()
    {
        // create the transaction data array.
        $data = [
            'id' => $this->id,
            'block_num' => $this->blockNumber,
            'trx_num' => $this->transactionNumber,
            'expired' => $this->expired,
            'ref_block_num' => $this->referenceBlockNumber,
            'ref_block_prefix' => $this->referenceBlockPrefix,
            'expiration' => $this->expiration ? $this->expiration->format('Y-m-d\TH:i:s') : null,
            'operations' => $this->operations->toArray(),
            'extensions' => $this->extensions->toArray(),
            'signatures' => $this->signatures->toArray(),
        ];

        // sort the transaction data by its keys/
        ksort($data);

        // return the result array
        return [ 'result' => $data ];
    }
}