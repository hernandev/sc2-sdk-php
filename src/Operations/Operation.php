<?php

namespace SteemConnect\Operations;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use SteemConnect\Config\Config;
use SteemConnect\Operations\Common\HasName;
use SteemConnect\Operations\Common\HasParameters;
use SteemConnect\Operations\Common\SerializesJson;
use SteemConnect\Contracts\Operations\Operation as OperationContract;

/**
 * Class Operation.
 *
 * Base broadcasting operation implementation.
 *
 */
class Operation implements OperationContract
{
    // Enable parameter functionality.
    use HasParameters;

    // Enable a name on the operation itself.
    use HasName;

    // Enable json serialization of inner / custom / json parameters.
    use SerializesJson;

    /**
     * @var Config Instance of the configuration.
     */
    protected $config;

    /**
     * Operation constructor.
     *
     * @param string $name
     * @param array $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        // set the name directly, because of fluent interfaces.
        $this->name = $name;
        // set parameters directly, because of fluent interfaces.
        $this->parameters = $parameters;
    }

    /**
     * Magic getter for operations.
     *
     * @param $name
     *
     * @return array|null|Operation|SubOperation|string
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return $this->getParameter($name);
    }
}