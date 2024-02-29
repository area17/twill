<?php

namespace App\TwillApi\V1\Disciplines;

use App\Models\Discipline;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use A17\Twill\API\JsonApi\V1\Models\ModelSchema;

class DisciplineSchema extends ModelSchema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Discipline::class;

    /**
     * Show published or draft status attribute
     *
     * @var boolean
     */
    protected bool $publishedAttribute = true;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        return array_merge($fields, [
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            Str::make('title'),
            HasMany::make('works')
        ]);
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        $filters = parent::filters();

        return array_merge($filters, [
            //
        ]);
    }
}
