<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\MediaRequest;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignS3UploadListener;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Input;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends ModuleController implements SignS3UploadListener
{
    protected $moduleName = 'medias';

    protected $namespace = 'A17\Twill';

    protected $defaultOrders = [
        'id' => 'desc',
    ];

    protected $defaultFilters = [
        'search' => 'search',
        'tag' => 'tag_id',
    ];

    protected $perPage = 40;

    protected $endpointType;

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = config('twill.media_library.endpoint_type');
    }

    public function index($parentModuleId = null)
    {
        if (request()->has('except')) {
            $prependScope['exceptIds'] = request('except');
        }

        return $this->getIndexData($prependScope ?? []);
    }

    public function getIndexData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        $items = $this->getIndexItems($scopes);

        return [
            'items' => $items->map(function ($item) {
                return $this->buildMedia($item);
            })->toArray(),
            'maxPage' => $items->lastPage(),
            'total' => $items->total(),
            'tags' => $this->repository->getTagsList(),
        ];
    }

    private function buildMedia($item)
    {
        return $item->toCmsArray() + [
            'tags' => $item->tags->map(function ($tag) {
                return $tag->name;
            }),
            'deleteUrl' => $item->canDeleteSafely() ? moduleRoute($this->moduleName, $this->routePrefix, 'destroy', $item->id) : null,
            'updateUrl' => route('admin.media-library.medias.single-update'),
            'updateBulkUrl' => route('admin.media-library.medias.bulk-update'),
            'deleteBulkUrl' => route('admin.media-library.medias.bulk-delete'),
            'metadatas' => [
                'default' => [
                    'caption' => $item->caption,
                    'altText' => $item->alt_text,
                    'video' => null,
                ],
                'custom' => [
                    'caption' => null,
                    'altText' => null,
                    'video' => null,
                ],
            ],
        ];
    }

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

    public function store($parentModuleId = null)
    {
        $request = app(MediaRequest::class);

        if ($this->endpointType === 'local') {
            $media = $this->storeFile($request);
        } else {
            $media = $this->storeReference($request);
        }

        return response()->json(['media' => $this->buildMedia($media), 'success' => true], 200);
    }

    public function storeFile($request)
    {
        $originalFilename = $request->input('qqfilename');

        $filename = sanitizeFilename($originalFilename);

        $fileDirectory =  $request->input('unique_folder_name');

        $disk = config('twill.media_library.disk');

        $request->file('qqfile')->storeAs($fileDirectory, $filename, $disk);

        $filePath = Storage::disk($disk)->path($fileDirectory . '/' . $filename);

        list($w, $h) = getimagesize($filePath);

        $fields = [
            'uuid' => $request->input('unique_folder_name') . '/' . $filename,
            'filename' => $originalFilename,
            'width' => $w,
            'height' => $h,
        ];

        return $this->repository->create($fields);
    }

    public function storeReference($request)
    {
        $fields = [
            'uuid' => $request->input('key'),
            'filename' => $request->input('name'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
        ];

        return $this->repository->create($fields);
    }

    public function singleUpdate()
    {
        $this->repository->update(
            $this->request->input('id'),
            $this->request->only('alt_text', 'caption', 'tags')
        );

        return response()->json([
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }

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
                return $this->buildMedia($item);
            })->toArray(),
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }

    public function signS3Upload(Request $request, SignS3Upload $signS3Upload)
    {
        return $signS3Upload->fromPolicy($request->getContent(), $this, config('twill.media_library.disk'));
    }

    public function policyIsSigned($signedPolicy)
    {
        return response()->json($signedPolicy, 200);
    }

    public function policyIsNotValid()
    {
        return response()->json(["invalid" => true], 500);
    }
}
