<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Config\Repository as Config;
use A17\Twill\Services\Uploader\SignS3Upload;
use Illuminate\Contracts\Foundation\Application;
use A17\Twill\Services\Uploader\SignAzureUpload;
use A17\Twill\Services\Uploader\SignUploadListener;

abstract class LibraryController extends ModuleController implements SignUploadListener
{
    /**
     * @var string
     */
    protected $namespace = 'A17\Twill';

    /**
     * @var array
     */
    protected $defaultOrders = [
        'id' => 'desc',
    ];

    /**
     * @var array
     */
    protected $defaultFilters = [
        'search' => 'search',
        'tag' => 'tag_id',
        'unused' => 'unused',
    ];

    /**
     * @var int
     */
    protected $perPage = 40;

    /**
     * @var string
     */
    protected $endpointType;

    /**
     * @var Illuminate\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @param int|null $parentModuleId
     * @return array
     */
    public function index($parentModuleId = null)
    {
        if ($this->request->has('except')) {
            $prependScope['exceptIds'] = $this->request->get('except');
        }

        return $this->getIndexData($prependScope ?? []);
    }

    /**
     * @return array
     */
    protected function getRequestFilters()
    {
        if ($this->request->has('search')) {
            $requestFilters['search'] = $this->request->get('search');
        }

        if ($this->request->has('tag')) {
            $requestFilters['tag'] = $this->request->get('tag');
        }

        if (
            $this->request->has('unused') &&
            (int) $this->request->unused === 1
        ) {
            $requestFilters['unused'] = $this->request->get('unused');
        }

        return $requestFilters ?? [];
    }

    /**
     * @param Request $request
     * @param SignS3Upload $signS3Upload
     * @return mixed
     */
    public function signS3Upload(Request $request, SignS3Upload $signS3Upload)
    {
        return $signS3Upload->fromPolicy(
            $request->getContent(),
            $this,
            $this->config->get('twill.media_library.disk')
        );
    }

    /**
     * @param Request $request
     * @param SignAzureUpload $signAzureUpload
     * @return mixed
     */
    public function signAzureUpload(
        Request $request,
        SignAzureUpload $signAzureUpload
    ) {
        return $signAzureUpload->getSasUrl(
            $request,
            $this,
            $this->config->get('twill.media_library.disk')
        );
    }

    /**
     * @param $signature
     * @param bool $isJsonResponse
     * @return mixed
     */
    public function uploadIsSigned($signature, $isJsonResponse = true)
    {
        return $isJsonResponse
            ? $this->responseFactory->json($signature, 200)
            : $this->responseFactory->make($signature, 200, [
                'Content-Type' => 'text/plain',
            ]);
    }

    /**
     * @return JsonResponse
     */
    public function uploadIsNotValid()
    {
        return $this->responseFactory->json(['invalid' => true], 500);
    }

    /**
     * @param string $originalFilename
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function makeFilename($filename, $request)
    {
        if (
            !$this->config->get('twill.media_library.deduplication.enabled') ||
            !$this->config->get('twill.media_library.deduplication.flatten_filename')
        ) {
            return $filename;
        }

        $hash = $this->makeFileSha1($request);

        $parsed = pathinfo($filename);

        return $hash . ".{$parsed['extension']}";
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    private function makeImageFolderName(Request $request)
    {
        if (!$this->config->get('twill.media_library.deduplication.enabled')) {
            return $request->input('unique_folder_name');
        }

        if (
            !$this->config->get('twill.media_library.deduplication.flatten_filename')
        ) {
            return $this->makeFileSha1($request);
        }

        return '';
    }

    /**
     * @param $fileDirectory
     * @param string $filename
     * @return string
     */
    protected function makeUUID(
        $fileDirectory,
        string $filename,
        $request
    ): string {
        if (
            !$this->config->get('twill.media_library.deduplication.enabled') ||
            !$this->config->get('twill.media_library.deduplication.flatten_filename')
        ) {
            return $fileDirectory . '/' . $filename;
        }

        if (
            $this->config->get('twill.media_library.deduplication.flatten_filename')
        ) {
            $fileDirectory = $this->makeFileSha1($request);
        }

        $parsed = pathinfo($filename);

        return $fileDirectory . ".{$parsed['extension']}";
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function makeFileSha1(Request $request): string
    {
        return sha1(file_get_contents($request->file('qqfile')->getRealPath()));
    }

    /**
     * @return bool
     */
    private function shouldReplaceMedia($id)
    {
        return filled($id) && $id !== 'null' && $id !== 'undefined'
            ? $this->repository->whereId($id)->exists()
            : false;
    }

    /**
     * @param Request $request
     * @return Media
     */
    public function storeFile($request)
    {
        $originalFilename = $request->input('qqfilename');

        $filename = $this->makeFilename($originalFilename, $request);

        $fileDirectory = $this->makeImageFolderName($request);

        $uuid = $this->makeUUID($fileDirectory, $filename, $request);

        if (
            $this->config->get(
                'twill.media_library.prefix_uuid_with_local_path',
                false
            )
        ) {
            $prefix =
                trim(
                    $this->config->get('twill.media_library.local_path'),
                    '/ '
                ) . '/';
            $fileDirectory = $prefix . $fileDirectory;
            $uuid = $prefix . $uuid;
        }

        $disk = $this->config->get('twill.media_library.disk');

        $request->file('qqfile')->storeAs($fileDirectory, $filename, $disk);

        $filePath = Storage::disk($disk)->path(
            $fileDirectory . '/' . $filename
        );

        [$w, $h] = getimagesize($filePath);

        $fields = [
            'uuid' => $uuid,
            'filename' => $originalFilename,
            'width' => $w,
            'height' => $h,
        ];

        if (
            $this->shouldReplaceMedia(
                $id = $request->input('media_to_replace_id')
            )
        ) {
            $media = $this->repository->whereId($id)->first();
            $this->repository->afterDelete($media);
            $media->replace($fields);
            return $media->fresh();
        } else {
            return $this->repository->firstOrCreate(['uuid' => $uuid], $fields);
        }
    }
}
