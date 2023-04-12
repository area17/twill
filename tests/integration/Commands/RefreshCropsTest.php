<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Repositories\MediaRepository;
use A17\Twill\Tests\Integration\TestCase;
use App\Repositories\AuthorRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Carbon;

class RefreshCropsTest extends TestCase
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    public ?string $example = 'tests-modules';

    public function setUp(): void
    {
        parent::setUp();

        $this->db = app(DatabaseManager::class);
    }

    public function createAuthor($name = 'Alice')
    {
        return app(AuthorRepository::class)->create([
            'name' => [
                'en' => $name,
                'fr' => $name,
            ],
            'published' => true,
        ]);
    }

    public function createMedia($attributes = [])
    {
        return app(MediaRepository::class)->create(array_merge([
            'filename' => 'not-a-real-image.jpg',
            'uuid' => uniqid() . '/not-a-real-image.jpg',
            'width' => 1920,
            'height' => 1080,
        ], $attributes));
    }

    public function createMediable($attributes = [])
    {
        $this->db
            ->table(config('twill.mediables_table', 'twill_mediables'))
            ->insert(array_merge([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'mediable_id' => 1,
                'mediable_type' => 'App\\Models\\Author',
                'media_id' => 1,
                'role' => 'avatar',
                'crop' => 'default',
                'lqip_data' => null,
                'ratio' => 'landscape',
                'metadatas' => '{"video": null, "altText": null, "caption": null}',
                'locale' => 'en',
            ], $attributes));
    }

    public function mediables()
    {
        return $this->db->table(config('twill.mediables_table', 'twill_mediables'));
    }

    public function testCanLocateShortModelName()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable();

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);
    }

    public function testCanLocateFullModelName()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable();

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);
    }

    public function testFailsIfModelNotFound()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable();

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Post',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(1);
    }

    public function testFailsIfRoleNotFound()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable();

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'photo',
            ])
            ->assertExitCode(1);
    }

    public function testFailsIfNoMediables()
    {
        $this->createAuthor();
        $this->createMedia();

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(1);
    }

    public function testCanDoDryRun()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable();

        $this->assertEquals(1, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
                '--dry' => 'true',
            ])
            ->assertExitCode(0);

        $this->assertEquals(1, $this->mediables()->count());
    }

    public function testCanGenerateMissingCrop()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable(['crop' => 'default']);

        $mediables = $this->mediables()->get();
        $this->assertEquals(1, $mediables->count());
        $this->assertEquals('default', $mediables[0]->crop);

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $mediables = $this->mediables()->get();
        $this->assertEquals(2, $mediables->count());
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('mobile', $mediables[1]->crop);
    }

    public function testCanDeleteUnusedCrop()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable(['crop' => 'default']);
        $this->createMediable(['crop' => 'mobile']);
        $this->createMediable(['crop' => 'unused']);

        $this->assertEquals(3, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $mediables = $this->mediables()->get();
        $this->assertEquals(2, $mediables->count());
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('mobile', $mediables[1]->crop);
    }

    public function testCanGenerateMissingCropForLocale()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable(['crop' => 'default', 'locale' => 'en']);
        $this->createMediable(['crop' => 'mobile', 'locale' => 'en']);
        $this->createMediable(['crop' => 'default', 'locale' => 'fr']);

        $this->assertEquals(3, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $mediables = $this->mediables()->get();
        $this->assertEquals(4, $mediables->count());
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('en', $mediables[0]->locale);
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('en', $mediables[1]->locale);
        $this->assertEquals('default', $mediables[2]->crop);
        $this->assertEquals('fr', $mediables[2]->locale);
        $this->assertEquals('mobile', $mediables[3]->crop);
        $this->assertEquals('fr', $mediables[3]->locale);
    }

    public function testCanDeleteUnusedCropForLocale()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable(['crop' => 'default', 'locale' => 'en']);
        $this->createMediable(['crop' => 'mobile', 'locale' => 'en']);
        $this->createMediable(['crop' => 'default', 'locale' => 'fr']);
        $this->createMediable(['crop' => 'mobile', 'locale' => 'fr']);
        $this->createMediable(['crop' => 'unused', 'locale' => 'fr']);

        $this->assertEquals(5, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $mediables = $this->mediables()->get();
        $this->assertEquals(4, $mediables->count());
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('en', $mediables[0]->locale);
        $this->assertEquals('default', $mediables[0]->crop);
        $this->assertEquals('en', $mediables[1]->locale);
        $this->assertEquals('default', $mediables[2]->crop);
        $this->assertEquals('fr', $mediables[2]->locale);
        $this->assertEquals('mobile', $mediables[3]->crop);
        $this->assertEquals('fr', $mediables[3]->locale);
    }

    public function testCanDoMultipleOperations()
    {
        // 6 crops
        $completeAuthors = collect([1, 2, 3])->map(function () {
            $author = $this->createAuthor();
            $media = $this->createMedia();
            $this->createMediable(['crop' => 'default', 'mediable_id' => $author->id, 'media_id' => $media->id]);
            $this->createMediable(['crop' => 'mobile', 'mediable_id' => $author->id, 'media_id' => $media->id]);

            return $author;
        });

        // 2 crops, 2 missing
        $authorsMissingCrops = collect([1, 2])->map(function () {
            $author = $this->createAuthor();
            $media = $this->createMedia();
            $this->createMediable(['crop' => 'default', 'mediable_id' => $author->id, 'media_id' => $media->id]);

            return $author;
        });

        // 15 crops, 5 unused
        $authorsWithUnusedCrops = collect([1, 2, 3, 4, 5])->map(function () {
            $author = $this->createAuthor();
            $media = $this->createMedia();
            $this->createMediable(['crop' => 'default', 'mediable_id' => $author->id, 'media_id' => $media->id]);
            $this->createMediable(['crop' => 'mobile', 'mediable_id' => $author->id, 'media_id' => $media->id]);
            $this->createMediable(['crop' => 'unused', 'mediable_id' => $author->id, 'media_id' => $media->id]);

            return $author;
        });

        $this->assertEquals(23, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        // 2 crop added, 5 crops removed
        $this->assertEquals(20, $this->mediables()->count());
    }

    public function testCanGenerateMissingCropForSlideshow()
    {
        $author = $this->createAuthor();

        $slideshow = collect([1, 2, 3, 4, 5])->map(function () use ($author) {
            $media = $this->createMedia();
            $this->createMediable(['crop' => 'default', 'mediable_id' => $author->id, 'media_id' => $media->id]);

            return $author;
        });

        $this->assertEquals(5, $this->mediables()->count());

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $this->assertEquals(10, $this->mediables()->count());
    }

    public function testGeneratedCropsUseCorrectRatio()
    {
        // missing mobile crop
        $author = $this->createAuthor();
        $media = $this->createMedia();
        $this->createMediable(['crop' => 'default', 'mediable_id' => $author->id, 'media_id' => $media->id]);

        // missing default crop
        $author2 = $this->createAuthor();
        $media2 = $this->createMedia();
        $this->createMediable(['crop' => 'mobile', 'mediable_id' => $author2->id, 'media_id' => $media2->id]);

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        // generated mobile crop
        $imageData = $author->imageAsArray('avatar', 'mobile');
        $this->assertEquals(1, $imageData['width'] / $imageData['height']);

        // generated default crop
        $imageData = $author2->imageAsArray('avatar', 'default');
        $this->assertEquals(16 / 9, $imageData['width'] / $imageData['height']);
    }

    public function testPreservesMetadataForGeneratedCrops()
    {
        $this->createAuthor();
        $this->createMedia();
        $this->createMediable([
            'crop' => 'default',
            'metadatas' => '{"video": "/video.mp4", "altText": "Lorem ipsum", "caption": "Lorem ipsum"}',
        ]);

        $this->artisan('twill:refresh-crops', [
                'modelName' => 'App\Models\Author',
                'roleName' => 'avatar',
            ])
            ->assertExitCode(0);

        $mediables = $this->mediables()->get();
        $this->assertEquals($mediables[0]->metadatas, $mediables[1]->metadatas);
    }
}
