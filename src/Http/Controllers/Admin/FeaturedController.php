<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Feature;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use Illuminate\Console\Application;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

class FeaturedController extends Controller
{
    /**
     * @var DB
     */
    protected $db;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @param DB $db
     * @param UrlGenerator $urlGenerator
     * @param Application $app
     */
    public function __construct(DB $db, UrlGenerator $urlGenerator, Application $app, ViewFactory $viewFactory)
    {
        parent::__construct($app);

        $this->db = $db;
        $this->urlGenerator = $urlGenerator;
        $this->app = $app;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $featuredSectionKey = $request->segment(count($request->segments()));
        $featuredSection = config("twill.buckets.$featuredSectionKey");
        $filters = json_decode($request->get('filter'), true) ?? [];

        $featuredSources = $this->getFeaturedSources($request, $featuredSection, $filters['search'] ?? '');

        $contentTypes = Collection::make($featuredSources)->map(function ($source, $sourceKey) {
            return [
                'label' => $source['name'],
                'value' => $sourceKey,
            ];
        })->values()->toArray();

        if ($request->has('content_type')) {
            $source = Arr::first($featuredSources, function ($source, $sourceKey) use ($request) {
                return $sourceKey == $request->get('content_type');
            });

            return [
                'source' => [
                    'content_type' => Arr::first($contentTypes, function ($contentTypeItem) use ($request) {
                        return $contentTypeItem['value'] == $request->get('content_type');
                    }),
                    'items' => $source['items'],
                ],
                'maxPage' => $source['maxPage'],
            ];
        }

        $buckets = $this->getFeaturedItemsByBucket($featuredSection, $featuredSectionKey);
        $firstSource = Arr::first($featuredSources);

        $routePrefix = 'featured';

        if (config('twill.bucketsRoutes') !== null) {
            $routePrefix = config('twill.bucketsRoutes')[$featuredSectionKey] ?? $routePrefix;
        }

        return $this->viewFactory->make('twill::layouts.buckets', [
            'dataSources' => [
                'selected' => Arr::first($contentTypes),
                'content_types' => $contentTypes,
            ],
            'items' => $buckets,
            'source' => [
                'content_type' => Arr::first($contentTypes),
                'items' => $firstSource['items'],
            ],
            'maxPage' => $firstSource['maxPage'],
            'offset' => $firstSource['offset'],
            'bucketSourceTitle' => $featuredSection['sourceHeaderTitle'] ?? null,
            'bucketsSectionIntro' => $featuredSection['sectionIntroText'] ?? null,
            'restricted' => $featuredSection['restricted'] ?? true,
            'saveUrl' => $this->urlGenerator->route("admin.$routePrefix.$featuredSectionKey.save"),
        ]);
    }

    private function getFeaturedItemsByBucket($featuredSection, $featuredSectionKey)
    {
        $bucketRouteConfig = config('twill.bucketsRoutes') ?? [$featuredSectionKey => 'featured'];
        return Collection::make($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use ($featuredSectionKey, $bucketRouteConfig) {
            $routePrefix = $bucketRouteConfig[$featuredSectionKey];
            return [
                'id' => $bucketKey,
                'name' => $bucket['name'],
                'max' => $bucket['max_items'],
                'acceptedSources' => Collection::make($bucket['bucketables'])->pluck('module'),
                'withToggleFeatured' => $bucket['with_starred_items'] ?? false,
                'toggleFeaturedLabels' => $bucket['starred_items_labels'] ?? [],
                'children' => Feature::where('bucket_key', $bucketKey)->with('featured')->get()->map(function ($feature) {
                    if (($item = $feature->featured) != null) {
                        $repository = $this->getRepository($feature->featured_type);
                        $withImage = classHasTrait($repository, HandleMedias::class);

                        return [
                            'id' => $item->id,
                            'name' => $item->titleInBucket ?? $item->title,
                            'edit' => $item->adminEditUrl ?? '',
                            'starred' => $feature->starred ?? false,
                            'content_type' => [
                                'label' => ucfirst($feature->featured_type),
                                'value' => $feature->featured_type,
                            ],
                        ] + ($withImage ? [
                            'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
                        ] : []);
                    }
                })->reject(function ($item) {
                    return is_null($item);
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    /**
     * @param Request $request
     * @param array $featuredSection
     * @param string|null $search
     * @return array
     */
    private function getFeaturedSources(Request $request, $featuredSection, $search = null)
    {
        $fetchedModules = [];
        $featuredSources = [];

        Collection::make($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use (&$fetchedModules, $search, $request) {
            return Collection::make($bucket['bucketables'])->mapWithKeys(function ($bucketable) use (&$fetchedModules, $bucketKey, $search, $request) {

                $module = $bucketable['module'];
                $repository = $this->getRepository($module);
                $translated = classHasTrait($repository, HandleTranslations::class);
                $withImage = classHasTrait($repository, HandleMedias::class);

                if ($search) {
                    $searchField = $bucketable['searchField'] ?? ($translated ? 'title' : '%title');
                    $scopes[$searchField] = $search;
                }

                $items = $fetchedModules[$module] ?? $repository->get(
                    $bucketable['with'] ?? [],
                    ($bucketable['scopes'] ?? []) + ($scopes ?? []),
                    $bucketable['orders'] ?? [],
                    $bucketable['per_page'] ?? $request->get('offset') ?? 10,
                    $forcePagination = true
                )->appends('bucketable', $module);

                $fetchedModules[$module] = $items;

                return [$module => [
                    'name' => $bucketable['name'] ?? ucfirst($module),
                    'items' => $items,
                    'translated' => $translated,
                    'withImage' => $withImage,
                ]];
            });
        })->each(function ($bucketables, $bucket) use (&$featuredSources) {
            $bucketables->each(function ($bucketableData, $bucketable) use ($bucket, &$featuredSources) {
                $featuredSources[$bucketable]['name'] = $bucketableData['name'];
                $featuredSources[$bucketable]['maxPage'] = $bucketableData['items']->lastPage();
                $featuredSources[$bucketable]['offset'] = $bucketableData['items']->perPage();
                $featuredSources[$bucketable]['items'] = $bucketableData['items']->map(function ($item) use ($bucketableData, $bucketable) {
                    return [
                        'id' => $item->id,
                        'name' => $item->titleInBucket ?? $item->title,
                        'edit' => $item->adminEditUrl ?? '',
                        'content_type' => [
                            'label' => $bucketableData['name'],
                            'value' => $bucketable,
                        ],
                    ] + ($bucketableData['translated'] ? [
                        'languages' => $item->getActiveLanguages(),
                    ] : []) + ($bucketableData['withImage'] ? [
                        'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
                    ] : []);
                })->toArray();
            });

        });

        return $featuredSources;
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Throwable
     */
    public function save(Request $request)
    {
        $this->db->transaction(function () use ($request) {
            Collection::make($request->get('buckets'))->each(function ($bucketables, $bucketKey) {
                Feature::where('bucket_key', $bucketKey)->delete();
                foreach (($bucketables ?? []) as $position => $bucketable) {
                    Feature::create([
                        'featured_id' => $bucketable['id'],
                        'featured_type' => $bucketable['type'],
                        'position' => $position + 1,
                        'bucket_key' => $bucketKey,
                        'starred' => $bucketable['starred'] ?? false,
                    ]);
                }
            });
        }, 5);

        fireCmsEvent('cms-buckets.saved');
    }

    private function getRepository($bucketable)
    {
        return $this->app->get(config('twill.namespace') . "\Repositories\\" . ucfirst(Str::singular($bucketable)) . "Repository");
    }
}
