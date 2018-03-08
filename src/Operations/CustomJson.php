<?php

namespace SteemConnect\Operations;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class CustomJson.
 *
 * Custom JSON operations base implementation.
 *
 * @property array $required_auths
 * @property array $required_posting_auths
 * @property SubOperation $json
 */
class CustomJson extends Operation
{
    /**
     * @var SubOperation.
     */
    protected $customOperation = 'follow';


    /**
     * @var array List of parameters on the operation.
     */
    protected $parameters = [
        'required_auths' => [],
        'required_posting_auths' => [],
    ];

    /**
     * @var string List of parameters to treat as JSON.
     */
    protected $jsonParameters = 'json';

    /**
     * Vote operation constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        // extract json string from custom_json.
        $json = array_get($parameters, $this->jsonParameters);

        // if there's a json sub operation data to be parsed.
        if ($json && Str::startsWith($json, '{')) {
            // decode it's data.
            $subOperationParameters = json_decode($json, true);
            // overwrite with a sub operation instance.
            Arr::set($parameters, $this->jsonParameters, new SubOperation($this->customOperation, $subOperationParameters));
        } else {
            Arr::set($parameters, $this->jsonParameters, new SubOperation($this->customOperation));
        }

        // cal parent constructor.
        parent::__construct('custom_json', array_merge($this->parameters, $parameters));
    }

    /**
     * Set the required auths for the custom json operation.
     *
     * @param array $requiredAuths
     *
     * @return self
     */
    public function setRequiredAuths(array $requiredAuths = []) : self
    {
        return $this->setParameter('required_auths', $requiredAuths);
    }

    /**
     * Returns the configured required auths for the operation.
     *
     * @return array
     */
    public function getRequiredAuths() : array
    {
        return $this->getParameter('required_auths');
    }

    /**
     * Set the required posting auths for the custom json operation.
     *
     * @param array $requiredPostingAuths
     *
     * @return self
     */
    public function setRequiredPostingAuths(array $requiredPostingAuths = []) : self
    {
        return $this->setParameter('required_posting_auths', $requiredPostingAuths);
    }

    /**
     * Returns the configured required posting auths for the operation.
     *
     * @return array
     */
    public function getRequiredPostingAuths() : array
    {
        return $this->getParameter('required_posting_auths');
    }

    /**
     * Set the custom json operation id.
     *
     * @param string $id
     *
     * @return self
     */
    public function setId(string $id) : self
    {
        return $this->setParameter('id', $id);
    }

    /**
     * Returns the custom json operation id.
     *
     * @return null|string
     */
    public function getId() : ? string
    {
        return $this->getParameter('id');
    }

    /**
     * Get the sub operation instance, if any.
     *
     * @return null|SubOperation
     */
    public function getSubOperation() : ?SubOperation
    {
        return $this->getParameter($this->jsonParameters);
    }

    /**
     * Set the sub operation directly on the json attribute.
     *
     * @param SubOperation $subOperation
     *
     * @return self
     */
    public function setSubOperation(SubOperation $subOperation) : self
    {
        $this->setParameter($this->jsonParameters, $subOperation);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $value = parent::__get($name);

        if ($value) {
            return $value;
        }

        return $this->getSubOperation()->{$name};
    }
}