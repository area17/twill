<?php

/*
 * Twill note: The cartalyst/tags package has been abandoned, see https://cartalyst.com.
 * This will be removed in Twill 4, but we need it here to support Laravel 13 on Twill 3 without breaking changes.
 *
 * Original package comment / license:
 *
 * Part of the Tags package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Tags
 * @version    15.0.1
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2025, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Tags;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IlluminateTag extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'tags';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'slug',
        'count',
        'namespace',
    ];

    /**
     * The tagged entities model.
     *
     * @var string
     */
    protected static $taggedModel = 'Cartalyst\Tags\IlluminateTagged';

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        if ($this->exists) {
            $this->tagged()->delete();
        }

        return parent::delete();
    }

    /**
     * Returns the polymorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Returns this tag tagged entities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagged(): HasMany
    {
        return $this->hasMany(static::$taggedModel, 'tag_id');
    }

    /**
     * Finds a tag by its name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName(Builder $query, string $name): Builder
    {
        return $query->whereName($name);
    }

    /**
     * Finds a tag by its slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->whereSlug($slug);
    }

    /**
     * Returns the tagged entities model.
     *
     * @return string
     */
    public static function getTaggedModel(): string
    {
        return static::$taggedModel;
    }

    /**
     * Sets the tagged entities model.
     *
     * @param string $taggedModel
     *
     * @return void
     */
    public static function setTaggedModel(string $taggedModel): void
    {
        static::$taggedModel = $taggedModel;
    }
}
