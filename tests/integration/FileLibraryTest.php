<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\File;
use A17\Twill\Tests\Integration\Behaviors\CreatesFile;
use Illuminate\Http\UploadedFile;

class FileLibraryTest extends TestCase
{
    use CreatesFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListFiles(): void
    {
        $this->ajax('/twill/file-library/files', 'GET', [
            'page' => 1,
            'type' => 'image',
            'except' => [-1],
            'search' => '*',
            'tag' => '',
        ])->assertStatus(200);

        $this->assertJson($this->content());
    }

    public function testCanUploadFile(): void
    {
        $this->createFile();
    }

    public function testCanSingleUpdateFile(): void
    {
        $file = $this->createFile();

        $data = [
            'alt_text' => 'Black Normal 1200x800',
            'fieldsRemovedFromBulkEditing' => [],
            'id' => $file->id,
            'tags' => 'avatar,photo',
        ];

        $crawler = $this->ajax(
            '/twill/file-library/files/single-update',
            'PUT',
            $data
        );

        $crawler->assertStatus(200);

        $file->refresh();

        $this->assertEquals(
            $this->now->format('Y-m-d H:i'),
            $file->created_at->format('Y-m-d H:i')
        );

        $this->assertEquals(
            ['avatar', 'photo'],
            $file->tags
                ->pluck('slug')
                ->sort()
                ->toArray()
        );
    }

    public function testCanUpdateInBulk(): void
    {
        $files = collect();

        $files->push($this->createFile());
        $files->push($this->createFile());
        $files->push($this->createFile());

        $data = [
            'ids' => $files->pluck('id')->implode(','),
            'fieldsRemovedFromBulkEditing' => [],
            'tags' => collect(
                $tagsArray = ['avatar', 'image', 'file', 'photo']
            )->implode(','),
        ];

        $crawler = $this->ajax(
            '/twill/file-library/files/bulk-update',
            'PUT',
            $data
        );

        $crawler->assertStatus(200);

        $tags = collect(
            $files->reduce(function ($carry, $file) {
                return $carry + $file->tags->pluck('slug')->toArray();
            }, [])
        )
            ->sort()
            ->toArray();

        $this->assertEquals($tags, $tagsArray);
    }
}
