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

class IlluminateTagged extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'tagged';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;
}
