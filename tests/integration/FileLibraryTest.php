<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\File;
use Illuminate\Http\UploadedFile;

class FileLibraryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListFiles()
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

    public function createFile()
    {
        $this->login();

        $data = [
            'unique_folder_name' => ($folder = $this->faker->uuid),
            'qquuid' => ($qquuid = $this->faker->uuid),
            'qqfilename' => ($fileName =
                'file-' . $this->faker->numberBetween(1000, 9999) . '.jpg'),
            'qqtotalfilesize' => strlen(
                $file = file_get_contents(stubs('pdf/area17.pdf'))
            ),
            'qqfile' => UploadedFile::fake()->image($fileName),
        ];

        $this->ajax('/twill/file-library/files', 'POST', $data)->assertStatus(
            200
        );

        $this->assertJson($this->content());

        $file = File::where('filename', $fileName)->first();

        $this->assertEquals($fileName, $file->filename);

        $this->assertEquals(
            $this->now->format('Y-m-d H:i'),
            $file->created_at->format('Y-m-d H:i')
        );

        $localPath = env('FILE_LIBRARY_LOCAL_PATH');

        $this->assertFileExists(
            storage_path("app/public/{$localPath}/{$folder}/{$fileName}")
        );

        return $file;
    }

    public function testCanUploadFile()
    {
        $this->createFile();
    }

    public function testCanSingleUpdateFile()
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
            $file->tags
                ->pluck('slug')
                ->sort()
                ->toArray(),
            ['avatar', 'photo']
        );
    }

    public function testCanUpdateInBulk()
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
