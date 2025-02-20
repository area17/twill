<?php

namespace A17\Twill\Tests\Integration\Isolation;

use A17\Twill\Repositories\FileRepository;
use A17\Twill\Repositories\MediaRepository;
use A17\Twill\Tests\Integration\Behaviors\CreatesFile;
use A17\Twill\Tests\Integration\Behaviors\CreatesMedia;
use A17\Twill\Tests\Integration\TestCase;
use Illuminate\Support\Facades\Config;

class MediaFileDeleteTest extends TestCase
{
    use CreatesMedia;
    use CreatesFile;

    protected MediaRepository $mediaRepository;
    protected FileRepository $fileRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->login();

        $this->mediaRepository = app(MediaRepository::class);
        $this->fileRepository = app(FileRepository::class);
    }

    public function testDeleteMediaDoesNotCleansFolderWhenCascadeFalse(): void
    {
        Config::set('twill.media_library.cascade_delete', false);

        $media = $this->createMedia();

        [$path, $filename] = explode('/', $media->uuid);

        $filePath = storage_path('app/public/media-library/' . $path . '/' . $filename);

        $this->assertFileExists($filePath);

        $this->mediaRepository->delete($media->id);

        $this->assertFileExists($filePath);
    }

    public function testDeleteMediaCleansFolderWhenCascadeTrue(): void
    {
        Config::set('twill.media_library.cascade_delete', true);

        $media = $this->createMedia();

        [$path, $filename] = explode('/', $media->uuid);

        $directoryPath = storage_path('app/public/media-library/' . $path);
        $filePath = $directoryPath . '/' . $filename;

        $this->assertFileExists($filePath);

        $this->mediaRepository->delete($media->id);

        $this->assertFileDoesNotExist($filePath);
        $this->assertFileDoesNotExist($directoryPath);
    }

    public function testDeleteFileDoesNotCleansFolderWhenCascadeFalse(): void
    {
        Config::set('twill.file_library.cascade_delete', false);

        $file = $this->createFile();

        [$path, $filename] = explode('/', $file->uuid);

        $filePath = storage_path('app/public/file-library/' . $path . '/' . $filename);

        $this->assertFileExists($filePath);

        $this->fileRepository->delete($file->id);

        $this->assertFileExists($filePath);
    }

    public function testDeleteFileCleansFolderWhenCascadeTrue(): void
    {
        Config::set('twill.file_library.cascade_delete', true);

        $file = $this->createFile();

        [$path, $filename] = explode('/', $file->uuid);

        $directoryPath = storage_path('app/public/file-library/' . $path);
        $filePath = $directoryPath . '/' . $filename;

        $this->assertFileExists($filePath);

        $this->fileRepository->delete($file->id);

        $this->assertFileDoesNotExist($filePath);
        $this->assertFileDoesNotExist($directoryPath);
    }
}
