<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class MediaLibraryTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListMedias()
    {
        $this->ajax('/twill/media-library/medias', 'GET', [
            'page' => 1,
            'type' => 'image',
            'except' => [-1],
            'search' => '*',
            'tag' => '',
        ])->assertStatus(200);

        $this->assertJson($this->content());
    }

    public function createMedia()
    {
        $this->login();

        $data = [
            'unique_folder_name' => ($folder = $this->faker->uuid),
            'qquuid' => ($qquuid = $this->faker->uuid),
            'qqfilename' => ($fileName =
                'file-' . $this->faker->numberBetween(1000, 9999) . '.jpg'),
            'qqtotalfilesize' => strlen(
                $file = file_get_contents(stubs('images/area17.png'))
            ),
            'qqfile' => UploadedFile::fake()->image($fileName),
        ];

        $this->ajax('/twill/media-library/medias', 'POST', $data)->assertStatus(
            200
        );

        $this->assertJson($this->content());

        $media = Media::where('filename', $fileName)->first();

        $this->assertEquals($fileName, $media->filename);

        $this->assertEquals(
            $this->now->format('Y-m-d H:i'),
            $media->created_at->format('Y-m-d H:i')
        );

        $localPath = env('MEDIA_LIBRARY_LOCAL_PATH');

        $this->assertFileExists(
            storage_path("app/public/{$localPath}/{$folder}/{$fileName}")
        );

        return $media;
    }

    public function testCanUploadMedia()
    {
        $this->createMedia();
    }

    public function testCanSingleUpdateMedia()
    {
        $media = $this->createMedia();

        $data = [
            'alt_text' => 'Black Normal 1200x800',
            'fieldsRemovedFromBulkEditing' => [],
            'id' => $media->id,
            'tags' => 'avatar,photo',
        ];

        $crawler = $this->ajax(
            '/twill/media-library/medias/single-update',
            'PUT',
            $data
        );

        $crawler->assertStatus(200);

        $media->refresh();

        $this->assertEquals(
            $this->now->format('Y-m-d H:i'),
            $media->created_at->format('Y-m-d H:i')
        );

        $this->assertEquals(
            $media->tags
                ->pluck('slug')
                ->sort()
                ->toArray(),
            ['avatar', 'photo']
        );
    }

    public function testCanUpdateInBulk()
    {
        $medias = collect();

        $medias->push($this->createMedia());
        $medias->push($this->createMedia());
        $medias->push($this->createMedia());

        $data = [
            'ids' => $medias->pluck('id')->implode(','),
            'fieldsRemovedFromBulkEditing' => [],
            'tags' => collect(
                $tagsArray = ['avatar', 'image', 'media', 'photo']
            )->implode(','),
        ];

        $crawler = $this->ajax(
            '/twill/media-library/medias/bulk-update',
            'PUT',
            $data
        );

        $crawler->assertStatus(200);

        $tags = collect(
            $medias->reduce(function ($carry, $media) {
                return $carry + $media->tags->pluck('slug')->toArray();
            }, [])
        )
            ->sort()
            ->toArray();

        $this->assertEquals($tags, $tagsArray);
    }

    public function testCanAttachAndDeleteMediaWithModel(): void
    {
        $media = $this->createMedia();
        /** @var \A17\Twill\Models\Behaviors\HasMedias $author */
        $author = $this->createAuthor();
        $author->medias()->attach($media->id, ['metadatas' => '{}']);
        $author->save();

        // Refresh the media so that its reloaded from the db after the attach.
        $media->refresh();

        $this->assertCount(1, $author->medias);
        $this->assertEquals(0, $media->unused()->count());
        $this->assertFalse($media->refresh()->canDeleteSafely());

        // Should not be able to delete media here.
        $this->assertFalse($media->delete());

        // Check we cannot remove it via the api.
        $this->deleteJson(route('admin.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media was not moved to trash. Something wrong happened!',
            'variant' => 'error'
        ]);

        // Delete the author and make sure media is still used.
        $author->delete();
        $media->refresh();

        $this->assertCount(1, $author->medias);
        $this->assertEquals(0, $media->unused()->count());
        $this->assertFalse($media->refresh()->canDeleteSafely());

        // Should not be able to delete media here.
        $this->assertFalse($media->delete());

        // Check we continue to be unable to remove.
        $this->deleteJson(route('admin.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media was not moved to trash. Something wrong happened!',
            'variant' => 'error'
        ]);

        // Force Delete the author and make sure media is still used.
        $this->assertTrue($author->forceDelete());
        $media->refresh();

        $this->assertEquals(1, $media->unused()->count());
        $this->assertTrue($media->refresh()->canDeleteSafely());

        // Finally delete the media.
        $this->deleteJson(route('admin.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media moved to trash!',
            'variant' => 'success'
        ]);

        $this->assertEquals(0, $media->count());
    }
}
