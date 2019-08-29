<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\FileRequest;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignS3UploadListener;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class FileLibraryController extends ModuleController implements SignS3UploadListener
{
    /**
     * @var string
     */
    protected $moduleName = 'files';

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
    ];

    /**
     * @var int
     */
    protected $perPage = 40;

    /**
     * @var string
     */
    protected $endpointType;

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
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = $this->config->get('twill.file_library.endpoint_type');
    }

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

        return $requestFilters ?? [];
    }

    /**
     * @param int|null $parentModuleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($parentModuleId = null)
    {
        $request = $this->app->get(FileRequest::class);

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
    public function storeFile($request)
    {
        $filename = $request->input('qqfilename');

        $cleanFilename = preg_replace("/\s+/i", "-", $filename);

        $fileDirectory = $request->input('unique_folder_name');

        $uuid = $request->input('unique_folder_name') . '/' . $cleanFilename;

        if ($this->config->get('twill.file_library.prefix_uuid_with_local_path', false)) {
            $prefix = trim($this->config->get('twill.file_library.local_path'), '/ ') . '/';
            $fileDirectory = $prefix . $fileDirectory;
            $uuid = $prefix . $uuid;
        }

        $disk = $this->config->get('twill.file_library.disk');

        $request->file('qqfile')->storeAs($fileDirectory, $cleanFilename, $disk);

        $fields = [
            'uuid' => $uuid,
            'filename' => $cleanFilename,
            'size' => $request->input('qqtotalfilesize'),
        ];

        return $this->repository->create($fields);
    }

    /**
     * @param Request $request
     * @return \A17\Twill\Models\File
     */
    public function storeReference($request)
    {
        $fields = [
            'uuid' => $request->input('key'),
            'filename' => $request->input('name'),
        ];

        return $this->repository->create($fields);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * @param Request $request
     * @param SignS3Upload $signS3Upload
     * @return mixed
     */
    public function signS3Upload(Request $request, SignS3Upload $signS3Upload)
    {
        return $signS3Upload->fromPolicy($request->getContent(), $this, $this->config->get('twill.file_library.disk'));
    }

    /**
     * @param mixed $signedPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function policyIsSigned($signedPolicy)
    {
        return $this->responseFactory->json($signedPolicy, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function policyIsNotValid()
    {
        return $this->responseFactory->json(["invalid" => true], 500);
    }
}
