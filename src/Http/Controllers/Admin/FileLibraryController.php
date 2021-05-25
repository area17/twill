<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Config\Repository as Config;
use A17\Twill\Http\Requests\Admin\FileRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Container\BindingResolutionException;

class FileLibraryController extends LibraryController
{
    /**
     * @var string
     */
    protected $moduleName = 'files';

    /**
     * @var Illuminate\Routing\UrlGenerator
     */
    protected $urlGenerator;

    public function __construct(
        Application $app,
        Request $request,
        UrlGenerator $urlGenerator,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        parent::__construct($app, $request);
        $this->urlGenerator = $urlGenerator;
        $this->responseFactory = $responseFactory;
        $this->config = $config;

        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'signAzureUpload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = $this->config->get('twill.file_library.endpoint_type');
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
                return $this->buildFile($item);
            })->toArray(),
            'maxPage' => $items->lastPage(),
            'total' => $items->total(),
            'tags' => $this->repository->getTagsList(),
        ];
    }

    /**
     * @param \A17\Twill\Models\File $item
     * @return array
     */
    private function buildFile($item)
    {
        return $item->toCmsArray() + [
            'tags' => $item->tags->map(function ($tag) {
                return $tag->name;
            }),
            'deleteUrl' => $item->canDeleteSafely() ? moduleRoute($this->moduleName, $this->routePrefix, 'destroy', $item->id) : null,
            'updateUrl' => $this->urlGenerator->route('admin.file-library.files.single-update'),
            'updateBulkUrl' => $this->urlGenerator->route('admin.file-library.files.bulk-update'),
            'deleteBulkUrl' => $this->urlGenerator->route('admin.file-library.files.bulk-delete'),
        ];
    }

    /**
     * @param int|null $parentModuleId
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function store($parentModuleId = null)
    {
        $request = $this->app->make(FileRequest::class);

        if ($this->endpointType === 'local') {
            $file = $this->storeFile($request);
        } else {
            $file = $this->storeReference($request);
        }

        return $this->responseFactory->json(['media' => $this->buildFile($file), 'success' => true], 200);
    }

    /**
     * @param Request $request
     * @return \A17\Twill\Models\File
     */
    public function storeReference($request)
    {
        $fields = [
            'uuid' => $request->input('key') ?? $request->input('blob'),
            'filename' => $request->input('name'),
        ];

        if ($this->shouldReplaceFile($id = $request->input('media_to_replace_id'))) {
            $file = $this->repository->whereId($id)->first();
            $this->repository->afterDelete($file);
            $file->update($fields);
            return $file->fresh();
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
            $this->request->only('tags')
        );

        return $this->responseFactory->json([], 200);
    }

    /**
     * @return JsonResponse
     */
    public function bulkUpdate()
    {
        $ids = explode(',', $this->request->input('ids'));

        $previousCommonTags = $this->repository->getTags(null, $ids);
        $newTags = array_filter(explode(',', $this->request->input('tags')));

        foreach ($ids as $id) {
            $this->repository->update($id, ['bulk_tags' => $newTags, 'previous_common_tags' => $previousCommonTags]);
        }

        $scopes = $this->filterScope(['id' => $ids]);
        $items = $this->getIndexItems($scopes);

        return $this->responseFactory->json([
            'items' => $items->map(function ($item) {
                return $this->buildFile($item);
            })->toArray(),
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }
}
