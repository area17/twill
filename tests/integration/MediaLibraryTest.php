<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Tests\Integration\Behaviors\CreatesMedia;

class MediaLibraryTest extends ModulesTestBase
{
    use CreatesMedia;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListMedias(): void
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

    public function testCanUploadMedia(): void
    {
        $this->createMedia();
    }

    public function testCanSingleUpdateMedia(): void
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
            ['avatar', 'photo'],
            $media->tags
                ->pluck('slug')
                ->sort()
                ->toArray()
        );
    }

    public function testCanUpdateInBulk(): void
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
        $this->deleteJson(route('twill.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media was not moved to trash. Something wrong happened!',
            'variant' => 'error',
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
        $this->deleteJson(route('twill.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media was not moved to trash. Something wrong happened!',
            'variant' => 'error',
        ]);

        // Force Delete the author and make sure media is still used.
        $this->assertTrue($author->forceDelete());
        $media->refresh();

        $this->assertEquals(1, $media->unused()->count());
        $this->assertTrue($media->refresh()->canDeleteSafely());

        // Finally delete the media.
        $this->deleteJson(route('twill.media-library.medias.destroy', ['media' => $media]))->assertJson([
            'message' => 'Media moved to trash!',
            'variant' => 'success',
        ]);

        $this->assertEquals(0, $media->count());
    }
}
