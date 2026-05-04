<?php

namespace A17\Twill\Tests\Integration\Tags\Stubs;

use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements TaggableInterface
{
    use TaggableTrait;

    public $table = 'posts';

    protected $fillable = ['title'];
}
