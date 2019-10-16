<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Media;
use Illuminate\Http\UploadedFile;

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
            'qquuid' => ($qquuid = $this->faker->uuid),
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

        $this->assertEquals(
            $this->now->format('Y-m-d H:i'),
            $media->created_at->format('Y-m-d H:i')
        );

        $localPath = env('MEDIA_LIBRARY_LOCAL_PATH');

        $this->assertFileExists(
            storage_path("app/public/{$localPath}/{$folder}/{$fileName}")
        );
    }
}
