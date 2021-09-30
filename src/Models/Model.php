<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\TwillModel;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel implements TaggableInterface, ModelInterface
{
    use TwillModel;
}
