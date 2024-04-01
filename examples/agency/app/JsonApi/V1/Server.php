<?php

namespace App\JsonApi\V1;

use A17\Twill\API\JsonApi\V1\Blocks\BlockSchema;
use A17\Twill\API\JsonApi\V1\Features\FeatureSchema;
use A17\Twill\API\JsonApi\V1\Files\FileSchema;
use A17\Twill\API\JsonApi\V1\Medias\MediaSchema;
use A17\Twill\API\JsonApi\V1\RelatedItems\RelatedItemSchema;
use A17\Twill\API\JsonApi\V1\Settings\SettingSchema;
use A17\Twill\API\JsonApi\V1\Tags\TagSchema;
use A17\Twill\API\JsonApi\V1\Users\UserSchema;
use App\TwillApi\V1\Abouts\AboutSchema;
use App\TwillApi\V1\Disciplines\DisciplineSchema;
use App\TwillApi\V1\Offices\OfficeSchema;
use App\TwillApi\V1\People\PersonSchema;
use App\TwillApi\V1\Sectors\SectorSchema;
use App\TwillApi\V1\Videos\VideoSchema;
use App\TwillApi\V1\Works\WorkSchema;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{

    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        // no-op
    }

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    protected function allSchemas(): array
    {
        return [
            BlockSchema::class,
            MediaSchema::class,
            FileSchema::class,
            TagSchema::class,
            UserSchema::class,
            SettingSchema::class,
            WorkSchema::class,
            RelatedItemSchema::class,
            SectorSchema::class,
            DisciplineSchema::class,
            OfficeSchema::class,
            AboutSchema::class,
            PersonSchema::class,
            FeatureSchema::class,
            VideoSchema::class
        ];
    }
}
