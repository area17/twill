<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\FileRequest;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignS3UploadListener;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Input;

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

    /**
     * @param Application $app
     * @param Request $request
     */
    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = config('twill.file_library.endpoint_type');
    }

    /**
     * @param int|null $parentModuleId
     * @return array
     */
    public function index($parentModuleId = null)
    {
        if (request()->has('except')) {
            $prependScope['exceptIds'] = request('except');
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
            'updateUrl' => route('admin.file-library.files.single-update'),
            'updateBulkUrl' => route('admin.file-library.files.bulk-update'),
            'deleteBulkUrl' => route('admin.file-library.files.bulk-delete'),
        ];
    }

    /**
     * @return array
     */
    protected function getRequestFilters()
    {
        if (request()->has('search')) {
            $requestFilters['search'] = request('search');
        }

        if (request()->has('tag')) {
            $requestFilters['tag'] = request('tag');
        }

        return $requestFilters ?? [];
    }

    /**
     * @param int|null $parentModuleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($parentModuleId = null)
    {
        $request = app(FileRequest::class);

        if ($this->endpointType === 'local') {
            $file = $this->storeFile($request);
        } else {
            $file = $this->storeReference($request);
        }

        return response()->json(['media' => $this->buildFile($file), 'success' => true], 200);
    }

    /**
     * @param Request $request
     * @return \A17\Twill\Models\File
     */
    public function storeFile($request)
    {
        $filename = $request->input('qqfilename');
        $cleanFilename = preg_replace("/\s+/i", "-", $filename);

        $fileDirectory = public_path(config('twill.file_library.local_path') . $request->input('unique_folder_name'));

        $request->file('qqfile')->move($fileDirectory, $cleanFilename);

        $fields = [
            'uuid' => $request->input('unique_folder_name') . '/' . $cleanFilename,
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

        return response()->json([], 200);
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

        return response()->json([
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
        return $signS3Upload->fromPolicy($request->getContent(), $this, config('twill.file_library.disk'));
    }

    /**
     * @param mixed $signedPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function policyIsSigned($signedPolicy)
    {
        return response()->json($signedPolicy, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function policyIsNotValid()
    {
        return response()->json(["invalid" => true], 500);
    }
}
