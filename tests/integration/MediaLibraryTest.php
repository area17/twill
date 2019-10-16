<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaLibraryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListMedias()
    {
        $crawler = $this->ajax('/twill/media-library/medias', 'GET', [
            'page' => 1,
            'type' => 'image',
            'except' => [-1],
            'search' => '*',
            'tag' => '',
        ]);

        $crawler->assertStatus(200);

        $this->assertJson($crawler->getContent());
    }

    public function testCanUploadMedia()
    {
        $this->login();

        $data = [
            'unique_folder_name' => ($folder = $this->faker->uuid),
            'qquuid' => $this->faker->uuid,
            'qqfilename' => ($fileName =
                'file-' . $this->faker->numberBetween(1000, 9999) . '.jpg'),
            'qqtotalfilesize' => strlen(
                $file = file_get_contents(stubs('images/area17.png'))
            ),
            'qqfile' => UploadedFile::fake()->image($fileName),
        ];

        $crawler = $this->ajax('/twill/media-library/medias', 'POST', $data);

        $crawler->assertStatus(200);

        $this->assertJson($crawler->getContent());

        $media = Media::where('filename', $fileName)->first();

        $this->assertEquals($fileName, $media->filename);

        $this->files->exists(
            storage_path("app/public/media-library/{$folder}/{$fileName}")
        );
    }
}
