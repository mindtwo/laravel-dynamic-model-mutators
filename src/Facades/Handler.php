<?php

namespace Mindtwo\DynamicMutators\Facades;

use Illuminate\Support\Facades\Facade;
use Mindtwo\DynamicMutators\Handler\MutationHandler;
use Mindtwo\DynamicMutators\Traits\HasMakeMapper;

/**
 * @see MutationHandler
 *
 * @method static \Mindtwo\DynamicMutators\Handler\MutationHandler make(array $arguments = [])
 */
class Handler extends Facade
{
    use HasMakeMapper;
}
