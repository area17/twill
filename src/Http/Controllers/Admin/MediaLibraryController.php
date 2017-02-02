<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Http\Requests\Admin\MediaRequest;
use A17\CmsToolkit\Services\Uploader\SignS3Upload;
use A17\CmsToolkit\Services\Uploader\SignS3UploadListener;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use ImageService;
use Input;

class MediaLibraryController extends ModuleController implements SignS3UploadListener
{
    protected $moduleName = 'medias';

    protected $namespace = 'A17\CmsToolkit';

    protected $defaultOrders = [
        'id' => 'desc',
    ];

    protected $defaultFilters = [
        'fSearch' => 'search',
        'fTag' => 'tag_id',
    ];

    protected $perPage = 30;

    protected $endpointType;

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['create', 'signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = config('cms-toolkit.media_library.endpoint_type');
    }

    public function index()
    {
        $libraryDisk = config('cms-toolkit.media_library.disk');

        $uploaderConfig = [
            'endpointType' => $this->endpointType,
            'endpoint' => $this->endpointType === 'local' ? route('admin.media-library.medias.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => route('admin.media-library.medias.store'),
            'completeEndpoint' => route('admin.media-library.medias.index') . "?new_uploads_ids=",
            'signatureEndpoint' => route('admin.media-library.sign-s3-upload'),
            'endpointRegion' => config('filesystems.disks.'. $libraryDisk .'.region', 'none'),
            'accessKey' => config('filesystems.disks.'. $libraryDisk .'.key', 'none'),
            'csrfToken' => csrf_token(),
        ];

        if ($this->request->ajax()) {
            return view("cms-toolkit::medias.list", $this->getIndexData());
        }

        // if we are currently uploading new medias, display the media library with those medias only and disable pagination
        if ($newUploads = $this->request->input('new_uploads_ids')) {
            $prependScope = ['id' => explode(',', $newUploads)];
            $this->perPage = -1;
        }

        return view("cms-toolkit::medias.index", $this->getIndexData($prependScope ?? []) + $uploaderConfig + $this->request->all());
    }

    public function indexData($request)
    {
        return [
            'fTagList' => [null => 'All tags'] + $this->repository->getTagsList(),
        ];
    }

    public function store()
    {
        $request = app(MediaRequest::class);

        if ($this->endpointType === 'local') {
            $media = $this->storeFile($request);
        } else {
            $media = $this->storeReference($request);
        }

        return response()->json(['id' => $media->id, 'success' => true], 200);
    }

    public function storeFile($request)
    {
        $originalFilename = $request->input('qqfilename');

        $filename = sanitizeFilename($originalFilename);

        $fileDirectory = public_path(config('cms-toolkit.media_library.local_path') . $request->input('unique_folder_name'));

        $request->file('qqfile')->move($fileDirectory, $filename);

        list($w, $h) = getimagesize($fileDirectory . '/' . $filename);

        $fields = [
            'uuid' => config('cms-toolkit.media_library.local_path') . $request->input('unique_folder_name') . '/' . $filename,
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

    public function edit($id)
    {
        $media = $this->repository->getById($id);
        return view('cms-toolkit::medias.form', [
            'isBulkUpdate' => false,
            'media' => $media,
            'tags' => $media->tags,
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
        ]);
    }

    public function bulkEdit()
    {
        $ids = $this->request->input('ids');
        $tags = $this->repository->getTags(null, $ids);
        return view('cms-toolkit::medias.form', [
            'isBulkUpdate' => true,
            'tags' => $tags,
            'media' => $this->repository->getById(last($ids)),
        ]);
    }

    public function tags()
    {
        $query = $this->request->input('query');
        $tags = $this->repository->getTags($query);
        return response()->json($tags, 200);
    }

    public function singleUpdate()
    {
        $this->repository->update(
            $this->request->input('id'),
            $this->request->only('alt_text', 'caption', 'tags')
        );

        return response()->json([], 200);
    }

    public function bulkUpdate()
    {
        $ids = explode(',', $this->request->input('ids'));
        $previousCommonTags = $this->repository->getTags(null, $ids);
        foreach ($ids as $id) {
            $this->repository->update($id, ['bulk_tags' => $this->request->input('tags'), 'previous_common_tags' => $previousCommonTags]);
        }

        return response()->json([], 200);
    }

    public function signS3Upload(Request $request, SignS3Upload $signS3Upload)
    {
        return $signS3Upload->fromPolicy($request->getContent(), $this, config('cms-toolkit.media_library.disk'));
    }

    public function policyIsSigned($signedPolicy)
    {
        return response()->json($signedPolicy, 200);
    }

    public function policyIsNotValid()
    {
        return response()->json(["invalid" => true], 500);
    }

    public function thumbnail()
    {
        $id = $this->request->input('id');
        $media = $this->repository->getById($id);
        return ImageService::getCmsUrl($media->uuid, ["w" => "150", "h" => "150"]);
    }

    public function crop()
    {
        $media = $this->repository->getById($this->request->input('id'));
        $crop = json_decode($this->request->input('crop'), true);

        return view('cms-toolkit::medias.crop_template')->withMedia($media)->withCrop($crop)->withBlockRatio($this->request->input('ratio'));
    }
}
