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
     * @var string List of attributes that must be serialized as JSON.
     */
    protected $jsonParameters = [
        'json'
    ];

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
                Arr::set($parameters, $parameter, utf8_encode(json_encode($value)));
            }

            // set the array as json string.
            if (is_object($value) && method_exists($value, 'toArray')) {
                Arr::set($parameters, $parameter, utf8_encode(json_encode($value->toArray())));
            }

            // set the value as it is, if string.
            if (is_string($value)) {
                Arr::set($parameters, $parameter, utf8_encode($value));
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