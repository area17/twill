<?php

namespace A17\Twill\Tests\Integration\Behaviors;

use A17\Twill\Models\File;
use A17\Twill\Models\Media;
use Illuminate\Http\UploadedFile;

trait CreatesFile
{
    public function createFile(): File
    {
        $data = [
            'unique_folder_name' => ($folder = $this->faker->uuid),
            'qquuid' => ($this->faker->uuid),
            'qqfilename' => ($fileName =
                'file-' . $this->faker->numberBetween(1000, 9999) . '.jpg'),
            'qqtotalfilesize' => strlen(
                $file = file_get_contents(stubs('pdf/area17.pdf'))
            ),
            'qqfile' => UploadedFile::fake()->image($fileName),
        ];

        $this->ajax('/twill/file-library/files', 'POST', $data)->assertStatus(200);

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
}
