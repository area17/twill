<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\FileRequest;
use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\TableFilters;
use A17\Twill\Services\Uploader\SignAzureUpload;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignUploadListener;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class FileLibraryController extends ModuleController implements SignUploadListener
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
     * @var int
     */
    protected $perPage = 40;

    /**
     * @var string
     */
    protected $endpointType;

    /**
     * @var Illuminate\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var Illuminate\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

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

        $this->middleware('can:access-media-library', ['only' => ['index']]);
        $this->middleware(
            'can:edit-media-library',
            ['only' => ['signS3Upload', 'signAzureUpload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]
        );
        $this->endpointType = $this->config->get('twill.file_library.endpoint_type');
    }

    public function setUpController(): void
    {
        $this->setSearchColumns(['filename']);
    }

    public function filters(): TableFilters
    {
        return TableFilters::make([
            BasicFilter::make()->queryString('tag')->apply(function (Builder $builder, ?int $value) {
                if ($value) {
                    $builder->whereHas('tags', function (Builder $builder) use ($value) {
                        $builder->where('tag_id', $value);
                    });
                }
                return $builder;
            }),
            BasicFilter::make()->queryString('unused')->apply(function (Builder $builder, ?bool $value) {
                if ($value) {
                    return $builder->unused();
                }
                return $builder;
            }),
        ]);
    }

    public function index(?int $parentModuleId = null): mixed
    {
        if ($this->request->has('except')) {
            $prependScope['exceptIds'] = $this->request->get('except');
        }

        return $this->getIndexData($prependScope ?? []);
    }

    protected function getIndexData(array $prependScope = []): array
    {
        $items = $this->getIndexItems($prependScope);

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
                'deleteUrl' => $item->canDeleteSafely() ? moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'destroy',
                    $item->id
                ) : null,
                'updateUrl' => $this->urlGenerator->route('twill.file-library.files.single-update'),
                'updateBulkUrl' => $this->urlGenerator->route('twill.file-library.files.bulk-update'),
                'deleteBulkUrl' => $this->urlGenerator->route('twill.file-library.files.bulk-delete'),
            ];
    }

    /**
     * @return array
     */
    protected function getRequestFilters(): array
    {
        if ($this->request->has('search')) {
            $requestFilters['search'] = $this->request->get('search');
        }

        if ($this->request->has('tag')) {
            $requestFilters['tag'] = $this->request->get('tag');
        }

        if ($this->request->has('unused') && (int)$this->request->unused === 1) {
            $requestFilters['unused'] = $this->request->get('unused');
        }

        return $requestFilters ?? [];
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

        if ($this->shouldReplaceFile($id = $request->input('media_to_replace_id'))) {
            $file = $this->repository->whereId($id)->first();
            $this->repository->afterDelete($file);
            $file->update($fields);
            return $file->fresh();
        }

        return $this->repository->create($fields);
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
        }

        return $this->repository->create($fields);
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

        $items = $this->getIndexItems(['id' => $ids]);

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
     * @param Request $request
     * @param SignAzureUpload $signAzureUpload
     * @return mixed
     */
    public function signAzureUpload(Request $request, SignAzureUpload $signAzureUpload)
    {
        return $signAzureUpload->getSasUrl($request, $this, $this->config->get('twill.file_library.disk'));
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

    /**
     * @return JsonResponse
     */
    public function uploadIsNotValid()
    {
        return $this->responseFactory->json(["invalid" => true], 500);
    }

    /**
     * @return bool
     */
    private function shouldReplaceFile($id)
    {
        return is_numeric($id) ? $this->repository->whereId($id)->exists() : false;
    }
}
