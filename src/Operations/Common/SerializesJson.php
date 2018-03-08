<?php

namespace SteemConnect\Operations\Common;

use Illuminate\Support\Arr;
use SteemConnect\Operations\Operation;

/**
 * Trait HasJsonMetadata.
 *
 * Handles JSON metadata attribute on an operation.
 */
trait SerializesJson
{
    /**
     * @var array List of attributes that must be serialized as JSON.
     */
    protected $jsonParameters = [];

    /**
     * Creates a parameters array, serializing fields marked as JSON string.
     *
     * @return array
     */
    public function serializeJsonParameters() : array
    {
        // copy the parameters array.
        $parameters = $this->parameters;

        // collect and loop on json parameters.
        collect($this->jsonParameters)->each(function ($parameter) use (&$parameters) {
            // get the current value.
            $value = Arr::get($parameters, $parameter);

            // if the current value is indeed an array...
            if (is_array($value)) {
                // rewrite it's value with the JSON serialized version.
                Arr::set($parameters, $parameter, json_encode($value));
            }

            if (is_object($value) && method_exists($value, 'toArray')) {
                Arr::set($parameters, $parameter, json_encode($value->toArray()));
            }
        });

        // return the parameters.
        return $parameters;
    }

    /**
     * Array representation of the operation.
     *
     * @return array
     */
    public function toArray()
    {
        // only call the getName if the method exists.
        if (method_exists($this, 'getName')) {
            return [ $this->getName(), $this->serializeJsonParameters() ];
        }

        // return the raw serialization method otherwise.
        return $this->serializeJsonParameters();
    }

    /**
     * JSON serialization method.
     *
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}