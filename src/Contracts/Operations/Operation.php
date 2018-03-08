<?php

namespace SteemConnect\Contracts\Operations;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Interface Operation.
 *
 * Simple interface for Operation implementations/
 */
interface Operation extends Arrayable, JsonSerializable
{
    //
}