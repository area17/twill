<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Models\Media;
use A17\Twill\Repositories\MediaRepository;
use A17\Twill\Services\Listings\Columns\Image;
use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ImageColumnTest extends ModulesTestBase
{
    public Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();

        [$media, $h, $w] = $this->helperUploadMedia();
        $this->media = $media;
        $this->author->medias()->attach($media->id, [
            'metadatas' => '{}',
            'role' => 'avatar',
            'crop' => 'default',
            'ratio' => 'landscape',
            'crop_x' => 0,
            'crop_y' => 0,
            'crop_w' => $w,
            'crop_h' => $h,
        ]);
        $this->author->medias()->attach($media->id, [
            'metadatas' => '{}',
            'role' => 'avatar',
            'crop' => 'default',
            'ratio' => 'portrait',
            'crop_x' => 0,
            'crop_y' => 0,
            'crop_w' => $w,
            'crop_h' => $h,
        ]);
    }

    private function helperUploadMedia(): array
    {
        $path = __DIR__ . '/../../../stubs/images/area17.png';

        $originalFilename = basename($path);
        $filename = sanitizeFilename($originalFilename);
        $fileDirectory = uniqid();
        $uuid = $fileDirectory . '/' . $filename;

        $disk = config('twill.media_library.disk');

        Storage::disk($disk)->put($uuid, file_get_contents($path));

        $filePath = Storage::disk($disk)->path($uuid);

        [$w, $h] = getimagesize($filePath);

        $fields = [
            'uuid' => $uuid,
            'filename' => $originalFilename,
            'width' => $w,
            'height' => $h,
        ];

        return [app(MediaRepository::class)->create($fields), $h, $w];
    }

    public function testColumnArray(): void
    {
        $column = Image::make()->field('image');

        $this->assertEquals(
            [
                "name" => "image",
                "label" => "Image",
                "visible" => true,
                "optional" => false,
                "sortable" => false,
                "html" => false,
                "variation" => "square",
                "specificType" => "thumbnail",
                'shrink' => true,
            ],
            $column->toColumnArray()
        );

        $column->rounded();

        $this->assertEquals(
            [
                "name" => "image",
                "label" => "Image",
                "visible" => true,
                "optional" => false,
                "sortable" => false,
                "html" => false,
                "variation" => "rounded",
                "specificType" => "thumbnail",
                "shrink" => true,
            ],
            $column->toColumnArray()
        );
    }

    public function testColumn(): void
    {
        $column = Image::make()->field('image');

        $imageUUID = Str::before($this->media->uuid, '/');

        $this->assertEquals(
            "http://twill.test/storage/media-library/{$imageUUID}/area17.png?fm=jpg&q=60&fit=crop&dpr=1&w=80&h=80&crop=398,258,0,0",
            urldecode($column->renderCell($this->author))
        );
    }

    public function testCustomRenderer(): void
    {
        $column = Image::make()->field('image')->customRender(function () {
            return "Test";
        });

        $this->assertEquals(
            "Test",
            urldecode($column->renderCell($this->author))
        );
    }

    public function testImageWithCustomParams(): void
    {
        $column = Image::make()->field('image')->mediaParams(['q' => 100, 'w' => 100, 'h' => 100]);

        $imageUUID = Str::before($this->media->uuid, '/');

        $this->assertEquals(
            "http://twill.test/storage/media-library/{$imageUUID}/area17.png?fm=jpg&q=100&fit=max&dpr=1&w=100&h=100&crop=398,258,0,0",
            urldecode($column->renderCell($this->author))
        );
    }

    public function testImageWithEmptyRole(): void
    {
        $column = Image::make()->field('image')->crop('role')->role('mobile');

        // As there is no crop, we expect a blank image.
        $this->assertEquals(
            "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",
            urldecode($column->renderCell($this->author))
        );
    }

    public function testImageWithEmptyRoleInDb(): void
    {
        // Empty is defined in @see Author
        $column = Image::make()->field('image')->role('empty');

        // As there is no crop, we expect a blank image.
        $this->assertEquals(
            "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",
            urldecode($column->renderCell($this->author))
        );
    }

    public function testNonExistingImageRole(): void
    {
        $column = Image::make()->field('image')->role('nonexisting');

        $this->expectException(\ErrorException::class);

        $column->renderCell($this->author);
    }

    public function testExceptionWhenMissingTrait(): void
    {
        $column = Image::make()->field('image');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot use image column on model not implementing HasMedias trait');
        $column->renderCell(new Category());
    }
}
