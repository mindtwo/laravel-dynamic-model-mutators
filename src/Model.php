<?php

namespace Mindtwo\DynamicMutators;

use Mindtwo\DynamicMutators\Traits\HasDynamicMutators;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasDynamicMutators;
}
