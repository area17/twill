<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Http\Requests\Admin\FileRequest;
use A17\CmsToolkit\Services\Uploader\SignS3Upload;
use A17\CmsToolkit\Services\Uploader\SignS3UploadListener;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Input;

class FileLibraryController extends ModuleController implements SignS3UploadListener
{
    protected $moduleName = 'files';

    protected $namespace = 'A17\CmsToolkit';

    protected $defaultOrders = [
        'id' => 'desc',
    ];

    protected $defaultFilters = [
        'fSearch' => 'search',
        'fTag' => 'tag_id',
    ];

    protected $perPage = 10;

    protected $endpointType;

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = config('cms-toolkit.file_library.endpoint_type');
    }

    public function index()
    {
        $libraryDisk = config('cms-toolkit.file_library.disk');

        $uploaderConfig = [
            'endpointType' => $this->endpointType,
            'endpoint' => $this->endpointType === 'local' ? route('admin.file-library.files.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => route('admin.file-library.files.store'),
            'completeEndpoint' => route('admin.file-library.files.index') . "?new_uploads_ids=",
            'signatureEndpoint' => route('admin.file-library.sign-s3-upload'),
            'endpointRegion' => config('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'accessKey' => config('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => config('cms-toolkit.file_library.acl'),
            'filesizeLimit' => config('cms-toolkit.file_library.filesize_limit'),
        ];

        if ($this->request->ajax()) {
            return view("cms-toolkit::files.list", $this->getIndexData());
        }

        // if we are currently uploading new files, display the file library with those files only and disable pagination
        if ($newUploads = $this->request->input('new_uploads_ids')) {
            $prependScope = ['id' => explode(',', $newUploads)];
            $this->perPage = -1;
        }

        return view("cms-toolkit::files.index", $this->getIndexData($prependScope ?? []) + $uploaderConfig + $this->request->all());
    }

    public function indexData($request)
    {
        return [
            'fTagList' => [null => 'All tags'] + $this->repository->getTagsList(),
        ];
    }

    public function store()
    {
        $request = app(FileRequest::class);

        if ($this->endpointType === 'local') {
            $media = $this->storeFile($request);
        } else {
            $media = $this->storeReference($request);
        }

        return response()->json(['id' => $media->id, 'success' => true], 200);
    }

    public function storeFile($request)
    {
        $filename = $request->input('qqfilename');
        $cleanFilename = preg_replace("/\s+/i", "-", $filename);

        $fileDirectory = public_path(config('cms-toolkit.file_library.local_path') . $request->input('unique_folder_name'));

        $request->file('qqfile')->move($fileDirectory, $cleanFilename);

        $fields = [
            'uuid' => $request->input('unique_folder_name') . '/' . $cleanFilename,
            'filename' => $cleanFilename,
            'size' => $request->input('qqtotalfilesize'),
        ];

        return $this->repository->create($fields);
    }

    public function storeReference($request)
    {
        $fields = [
            'uuid' => $request->input('key'),
            'filename' => $request->input('name'),
        ];

        return $this->repository->create($fields);
    }

    public function edit($id)
    {
        $file = $this->repository->getById($id);
        return view('cms-toolkit::files.form', [
            'isBulkUpdate' => false,
            'file' => $file,
            'tags' => $file->tags,
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
        ]);
    }

    public function bulkEdit()
    {
        $ids = $this->request->input('ids');
        $tags = $this->repository->getTags(null, $ids);
        return view('cms-toolkit::files.form', [
            'isBulkUpdate' => true,
            'tags' => $tags,
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
            $this->request->only('tags')
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
        return $signS3Upload->fromPolicy($request->getContent(), $this, config('cms-toolkit.file_library.disk'));
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
