<?php

namespace SteemConnect\Operations;


use SteemConnect\Operations\Common\HasName;
use SteemConnect\Operations\Common\HasParameters;
use SteemConnect\Contracts\Operations\Operation as OperationContract;
use SteemConnect\Operations\Common\SerializesJson;

/**
 * Class SubOperation.
 *
 * Sub operations are intended for usage inside custom_json operations.
 */
class SubOperation implements OperationContract
{
    // Enable operation naming.
    use HasName;

    // Enable parameter parsing.
    use HasParameters;

    // json serialization.
    use SerializesJson;

    /**
     * SubOperation constructor.
     *
     * @param string $name
     * @param array $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        // set sub operation name.
        $this->setName($name);

        // set sub operation parameters.
        $this->setParameters($parameters);
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