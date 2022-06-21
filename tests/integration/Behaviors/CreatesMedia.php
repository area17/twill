<?php

namespace A17\Twill\Tests\Integration\Behaviors;

use A17\Twill\Models\Media;
use Illuminate\Http\UploadedFile;

trait CreatesMedia
{
    public function createMedia(): Media
    {
        $data = [
            'unique_folder_name' => ($folder = $this->faker->uuid),
            'qquuid' => ($this->faker->uuid),
            'qqfilename' => ($fileName =
                'file-' . $this->faker->numberBetween(1000, 9999) . '.jpg'),
            'qqtotalfilesize' => strlen(
                $file = file_get_contents(stubs('images/area17.png'))
            ),
            'qqfile' => UploadedFile::fake()->image($fileName),
        ];

        $this->ajax('/twill/media-library/medias', 'POST', $data)
            ->assertStatus(200);

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
}
