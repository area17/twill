<?php

namespace App\TwillApi\V1\Works;

use App\Models\Work;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use A17\Twill\API\JsonApi\V1\Models\ModelSchema;

class WorkSchema extends ModelSchema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Work::class;

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
            Str::make('description'),
            Str::make('subtitle'),
            Str::make('case_study_text'),
            Str::make('video_url'),
            Str::make('year'),
            Str::make('client', 'client_name'),
            Boolean::make('autoplay'),
            Boolean::make('autoloop'),
            HasMany::make('sectors'),
            HasMany::make('disciplines')
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
