<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{
    use HasMedias, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'published',
        'can_delete',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    public function __construct(array $attributes = [])
    {
        $this->table = 'roles';

        parent::__construct($attributes);
    }

}
