<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Config\Repository as Config;
use A17\Twill\Http\Requests\Admin\MediaRequest;
use Illuminate\Contracts\Foundation\Application;

class MediaLibraryController extends LibraryController
{
    /**
     * @var string
     */
    protected $moduleName = 'medias';

    /**
     * @var array
     */
    protected $customFields;

    public function __construct(
        Application $app,
        Config $config,
        Request $request,
        ResponseFactory $responseFactory
    ) {
        parent::__construct($app, $request);
        $this->responseFactory = $responseFactory;
        $this->config = $config;

        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'signAzureUpload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = $this->config->get('twill.media_library.endpoint_type');
        $this->customFields = $this->config->get('twill.media_library.extra_metadatas_fields');
    }

    /**
     * @param array $prependScope
     * @return array
     */
    public function getIndexData($prependScope = [])
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
     * @param int|null $parentModuleId
     * @return
     */
    public function store($parentModuleId = null)
    {
        $request = $this->app->make(MediaRequest::class);
        if ($this->endpointType === 'local') {
            $media = $this->storeFile($request);
        } else {
            $media = $this->storeReference($request);
        }

        return $this->responseFactory->json(['media' => $media->toCmsArray(), 'success' => true], 200);
    }


    /**
     * @param Request $request
     * @return Media
     */
    public function storeReference($request)
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

    /**
     * @return JsonResponse
     */
    public function singleUpdate()
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

    /**
     * @return JsonResponse
     */
    public function bulkUpdate()
    {
        $ids = explode(',', $this->request->input('ids'));

        $metadatasFromRequest = $this->getExtraMetadatas()->reject(function ($meta) {
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
     * @return Collection
     */
    private function getExtraMetadatas()
    {
        return Collection::make($this->customFields)->mapWithKeys(function ($field) {
            $fieldInRequest = $this->request->get($field['name']);

            if (isset($field['type']) && $field['type'] === 'checkbox') {
                return [$field['name'] => $fieldInRequest ? Arr::first($fieldInRequest) : false];
            }

            return [$field['name'] => $fieldInRequest];
        });
    }
}
