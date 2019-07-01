<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\MediaRequest;
use A17\Twill\Services\Uploader\SignS3Upload;
use A17\Twill\Services\Uploader\SignS3UploadListener;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;

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

    protected $customFields;

    /**
     * @param Application $app
     * @param Request $request
     * @param Router $router
     * @param SessionStore $sessionStore
     * @param Redirector $redirector
     * @param UrlGenerator $urlGenerator
     * @param ViewFactory $viewFactory
     * @param AuthFactory $authFactory
     */
    public function __construct(
        Application $app,
        Request $request,
        Router $router,
        SessionStore $sessionStore,
        Redirector $redirector,
        UrlGenerator $urlGenerator,
        ViewFactory $viewFactory,
        AuthFactory $authFactory
    ) {
        parent::__construct($app, $request, $router, $sessionStore, $redirector, $urlGenerator, $viewFactory, $authFactory);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit', ['only' => ['signS3Upload', 'tags', 'store', 'singleUpdate', 'bulkUpdate']]);
        $this->endpointType = config('twill.media_library.endpoint_type');
        $this->customFields = config('twill.media_library.extra_metadatas_fields');
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
                return $item->toCmsArray();
            })->toArray(),
            'maxPage' => $items->lastPage(),
            'total' => $items->total(),
            'tags' => $this->repository->getTagsList(),
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
        $request = $this->app->get(MediaRequest::class);

        if ($this->endpointType === 'local') {
            $media = $this->storeFile($request);
        } else {
            $media = $this->storeReference($request);
        }

        return response()->json(['media' => $media->toCmsArray(), 'success' => true], 200);
    }

    public function storeFile($request)
    {
        $originalFilename = $request->input('qqfilename');

        $filename = sanitizeFilename($originalFilename);

        $fileDirectory = public_path(config('twill.media_library.local_path') . $request->input('unique_folder_name'));

        $request->file('qqfile')->move($fileDirectory, $filename);

        list($w, $h) = getimagesize($fileDirectory . '/' . $filename);

        $fields = [
            'uuid' => config('twill.media_library.local_path') . $request->input('unique_folder_name') . '/' . $filename,
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
            array_merge([
                'alt_text' => request('alt_text', null),
                'caption' => request('caption', null),
                'tags' => request('tags', null),
            ], $this->getExtraMetadatas()->toArray())
        );

        return response()->json([
            'tags' => $this->repository->getTagsList(),
        ], 200);
    }

    public function bulkUpdate()
    {
        $ids = explode(',', $this->request->input('ids'));

        $metadatasFromRequest = $this->getExtraMetadatas()->reject(function ($meta) {
            return is_null($meta);
        })->toArray();

        $extraMetadatas = array_diff_key($metadatasFromRequest, array_flip((array) request('fieldsRemovedFromBulkEditing', [])));

        if (in_array('tags', request('fieldsRemovedFromBulkEditing', []))) {
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

        return response()->json([
            'items' => $items->map(function ($item) {
                return $item->toCmsArray();
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

    private function getExtraMetadatas()
    {
        return Collection::make($this->customFields)->mapWithKeys(function ($field) {
            $fieldInRequest = request($field['name']);

            if (isset($field['type']) && $field['type'] === 'checkbox' && !$fieldInRequest) {
                return [$field['name'] => false];
            }

            return [$field['name'] => is_array($fieldInRequest) ? Arr::first($fieldInRequest) : $fieldInRequest];
        });
    }
}
