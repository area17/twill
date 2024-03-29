<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

abstract class Revision extends BaseModel
{
    public $timestamps = true;

    protected $with = ['user'];

    protected $fillable = [
        'payload',
        'user_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Remember to update this if you had fields to the fillable array here
        // this is to allow child classes to provide a custom foreign key in fillable
        if (count($this->fillable) == 2) {
            $this->fillable[] = strtolower(str_replace('Revision', '', get_called_class())) . '_id';
        }
    }

    public function user()
    {
        return $this->belongsTo(twillModel('user'));
    }

    public function getByUserAttribute()
    {
        return isset($this->user) ? $this->user->name : 'System';
    }

    public function isDraft(): bool
    {
        $data = json_decode($this->payload, true);

        $cmsSaveType = $data['cmsSaveType'] ?? '';

        return Str::startsWith($cmsSaveType, 'draft-revision');
    }
}
