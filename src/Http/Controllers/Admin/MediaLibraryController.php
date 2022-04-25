<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\MediaRequest;
use A17\Twill\Models\Media;
use A17\Twill\Services\Uploader\SignAzureUpload;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignUploadListener;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class MediaLibraryController extends ModuleController implements SignUploadListener
{
    /**
     * @var string
     */
    protected $moduleName = 'medias';

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
     * @var array
     */
    protected $customFields = [];

    public function __construct(
        Application $app,
        protected Config $config,
        Request $request,
        protected ResponseFactory $responseFactory
    ) {
        parent::__construct($app, $request);

        $this->middleware('can:access-media-library', ['only' => ['index']]);
        $this->middleware('can:edit-media-library', ['only' => ['signS3Upload', 'signAzureUpload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = $this->config->get('twill.media_library.endpoint_type');
        $this->customFields = $this->config->get('twill.media_library.extra_metadatas_fields');
    }

    /**
     * @param int|null $parentModuleId
     * @return array<string, mixed>
     */
    public function index($parentModuleId = null): array
    {
        if ($this->request->has('except')) {
            $prependScope['exceptIds'] = $this->request->get('except');
        }

        return $this->getIndexData($prependScope ?? []);
    }

    /**
     * @param mixed[] $prependScope
     * @return array<string, mixed>
     */
    protected function getIndexData(array $prependScope = []): array
    {
        $scopes = $this->filterScope($prependScope);
        $items = $this->getIndexItems($scopes);

        return [
            'items' => $items->map(function ($item) {
                return $item->toCmsArray();
            })->toArray(),
            'maxPage' => $items->lastPage(),
            'total' => $items->total(),
            'tags' => $this->repository->getTagsList(),
        ];
    }

    /**
     * @return mixed[]
     */
    protected function getRequestFilters(): array
    {
        if ($this->request->has('search')) {
            $requestFilters['search'] = $this->request->get('search');
        }

        if ($this->request->has('tag')) {
            $requestFilters['tag'] = $this->request->get('tag');
        }

        if ($this->request->has('unused') && (int) $this->request->unused === 1) {
            $requestFilters['unused'] = $this->request->get('unused');
        }

        return $requestFilters ?? [];
    }

    /**
     * @param int|null $parentModuleId
     * @return
     */
    public function store($parentModuleId = null)
    {
        $request = $this->app->make(MediaRequest::class);
        $media = $this->endpointType === 'local' ? $this->storeFile($request) : $this->storeReference($request);

        return $this->responseFactory->json(['media' => $media->toCmsArray(), 'success' => true], 200);
    }

    /**
     * @return Media
     */
    public function storeFile(\Illuminate\Http\Request $request)
    {
        $originalFilename = $request->input('qqfilename');

        $filename = sanitizeFilename($originalFilename);

        $fileDirectory = $request->input('unique_folder_name');

        $uuid = $request->input('unique_folder_name') . '/' . $filename;

        if ($this->config->get('twill.media_library.prefix_uuid_with_local_path', false)) {
            $prefix = trim($this->config->get('twill.media_library.local_path'), '/ ') . '/';
            $fileDirectory = $prefix . $fileDirectory;
            $uuid = $prefix . $uuid;
        }

        $disk = $this->config->get('twill.media_library.disk');

        $uploadedFile = $request->file('qqfile');

        list($w, $h) = getimagesize($uploadedFile->path());

        $uploadedFile->storeAs($fileDirectory, $filename, $disk);

        $fields = [
            'uuid' => $uuid,
            'filename' => $originalFilename,
            'width' => $w,
            'height' => $h,
        ];

        if ($this->shouldReplaceMedia($id = $request->input('media_to_replace_id'))) {
            $media = $this->repository->whereId($id)->first();
            $this->repository->afterDelete($media);
            $media->replace($fields);
            return $media->fresh();
        } else {
            return $this->repository->create($fields);
        }
    }

    /**
     * @return Media
     */
    public function storeReference(\Illuminate\Http\Request $request)
    {
        $fields = [
            'uuid' => $request->input('key') ?? $request->input('blob'),
            'filename' => $request->input('name'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
        ];

        if ($this->shouldReplaceMedia($id = $request->input('media_to_replace_id'))) {
            $media = $this->repository->whereId($id)->first();
            $this->repository->afterDelete($media);
            $media->update($fields);
            return $media->fresh();
        } else {
            return $this->repository->create($fields);
        }
    }

    public function singleUpdate(): \Illuminate\Http\JsonResponse
    {
        $this->repository->update(
            $this->request->input('id'),
            array_merge([
                'alt_text' => $this->request->get('alt_text', null),
                'caption' => $this->request->get('caption', null),
                'tags' => $this->request->get('tags', null),
            ], $this->getExtraMetadatas()->toArray())
        );

        return $this->responseFactory->json([
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }

    public function bulkUpdate(): \Illuminate\Http\JsonResponse
    {
        $ids = explode(',', $this->request->input('ids'));

        $metadatasFromRequest = $this->getExtraMetadatas()->reject(function ($meta): bool {
            return is_null($meta);
        })->toArray();

        $extraMetadatas = array_diff_key($metadatasFromRequest, array_flip((array) $this->request->get('fieldsRemovedFromBulkEditing', [])));

        if (in_array('tags', $this->request->get('fieldsRemovedFromBulkEditing', []))) {
            $this->repository->addIgnoreFieldsBeforeSave('bulk_tags');
        } else {
            $previousCommonTags = $this->repository->getTags(null, $ids);
            $newTags = array_filter(explode(',', $this->request->input('tags')));
        }

        foreach ($ids as $id) {
            $this->repository->update($id, [
                'bulk_tags' => $newTags ?? [],
                'previous_common_tags' => $previousCommonTags ?? [],
            ] + $extraMetadatas);
        }

        $scopes = $this->filterScope(['id' => $ids]);
        $items = $this->getIndexItems($scopes);

        return $this->responseFactory->json([
            'items' => $items->map(function ($item) {
                return $item->toCmsArray();
            })->toArray(),
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }

    /**
     * @return mixed
     */
    public function signS3Upload(Request $request, SignS3Upload $signS3Upload)
    {
        return $signS3Upload->fromPolicy($request->getContent(), $this, $this->config->get('twill.media_library.disk'));
    }

    /**
     * @return mixed
     */
    public function signAzureUpload(Request $request, SignAzureUpload $signAzureUpload)
    {
        return $signAzureUpload->getSasUrl($request, $this, $this->config->get('twill.media_library.disk'));
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
        : $this->responseFactory->make($signature, 200, ['Content-Type' => 'text/plain']);
    }

    public function uploadIsNotValid(): \Illuminate\Http\JsonResponse
    {
        return $this->responseFactory->json(["invalid" => true], 500);
    }

    private function getExtraMetadatas(): \Illuminate\Support\Collection
    {
        return Collection::make($this->customFields)->mapWithKeys(function ($field): array {
            $fieldInRequest = $this->request->get($field['name']);

            if (isset($field['type']) && $field['type'] === 'checkbox') {
                return [$field['name'] => $fieldInRequest ? Arr::first($fieldInRequest) : false];
            }

            return [$field['name'] => $fieldInRequest];
        });
    }

    /**
     * @return bool
     */
    private function shouldReplaceMedia($id)
    {
        return is_numeric($id) ? $this->repository->whereId($id)->exists() : false;
    }
}
