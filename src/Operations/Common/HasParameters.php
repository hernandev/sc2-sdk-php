<?php

namespace SteemConnect\Operations\Common;

use Illuminate\Support\Arr;
use SteemConnect\Operations\Operation;
use SteemConnect\Operations\SubOperation;

/**
 * Trait HasParameters.
 *
 * Common parameter setting.
 */
trait HasParameters
{
    /**
     * @var array List of parameters for the operation.
     */
    protected $parameters = [];

    /**
     * Directly set the parameters on the operation.
     *
     * @param array $parameters
     * @param bool $merge
     *
     * @return self
     */
    public function setParameters(array $parameters = [], bool $merge = false) : self
    {
        if ($merge) {
            $this->parameters = array_merge($this->parameters, $parameters);
        } else {
            $this->parameters = $parameters;
        }

        return $this;
    }

    /**
     * Get the parameters array as they are.
     *
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Fluent parameters setter.
     *
     * @param string $key
     * @param null $value
     *
     * @return $this
     */
    public function setParameter(string $key, $value = null) : self
    {
        // set a parameter value.
        Arr::set($this->parameters, $key, $value);

        // fluent return.
        return $this;
    }

    /**
     * Parameters getter.
     *
     * @param string $key
     *
     * @return null|string|array|Operation|SubOperation
     */
    public function getParameter(string $key)
    {
        return Arr::get($this->parameters, $key);
    }

    /**
     * Set a inner parameter.
     *
     * @param string $parameter
     * @param string $innerParameter
     * @param null $value
     *
     * @return self
     */
    public function setInnerParameter(string $parameter, string $innerParameter, $value = null) : self
    {
        Arr::set($this->parameters, "{$parameter}.{$innerParameter}", $value);

        return $this;
    }

    /**
     * Retrieve a inner parameter key.
     *
     * @param string $parameter
     * @param string $innerParameter
     *
     * @return mixed
     */
    public function getInnerParameter(string $parameter, string $innerParameter)
    {
        return Arr::get($this->parameters, "{$parameter}.{$innerParameter}");
    }

    /**
     * Unset a previously set parameter.
     *
     * @param string $key Parameter to remove.
     *
     * @return bool
     */
    public function forgetParameter(string $key)
    {
        // forget the element on the parameters array.
        Arr::forget($this->parameters, $key);

        // return the parameter key existence, to indicate operation success or failure.
        return Arr::has($this->parameters, $key);
    }

    /**
     * Unset a previously set inner parameter.
     *
     * @param string $parameter Parameter key to remove.
     * @param string $innerParameter Inner parameter key to remove.
     *
     * @return bool
     */
    public function forgetInnerParameter(string $parameter, string $innerParameter)
    {
        // forget the element on the parameters array.
        Arr::forget($this->parameters, "{$parameter}.{$innerParameter}");

        // return the parameter key existence, to indicate operation success or failure.
        return Arr::has($this->parameters, "{$parameter}.{$innerParameter}");
    }
}