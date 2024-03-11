<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Feature;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
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
     * @var Application
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Application $app, Config $config)
    {
        parent::__construct();
        $this->app = $app;
        $this->config = $config;
    }

    public function index(Request $request, ViewFactory $viewFactory, UrlGenerator $urlGenerator)
    {
        $featuredSectionKey = $request->segment(count($request->segments()));
        $featuredSection = $this->config->get("twill.buckets.$featuredSectionKey");
        $filters = json_decode($request->get('filter'), true) ?? [];

        $featuredSources = $this->getFeaturedSources($request, $featuredSection, $filters['search'] ?? '');

        $contentTypes = Collection::make($featuredSources)->map(function ($source, $sourceKey) {
            return [
                'label' => $source['name'],
                'value' => $sourceKey,
            ];
        })->values()->toArray();

        if ($request->has('content_type')) {
            $source = $featuredSources[$request->get('content_type')] ?? null;

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

        if ($this->config->get('twill.bucketsRoutes') !== null) {
            $routePrefix = $this->config->get('twill.bucketsRoutes')[$featuredSectionKey] ?? $routePrefix;
        }

        return $viewFactory->make('twill::layouts.buckets', [
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
            'bucketSectionLinks' => $featuredSection['sectionIntroLinks'] ?? null,
            'bucketSourceTitle' => $featuredSection['sourceHeaderTitle'] ?? null,
            'bucketsSectionIntro' => $featuredSection['sectionIntroText'] ?? null,
            'restricted' => $featuredSection['restricted'] ?? true,
            'saveUrl' => $urlGenerator->route(config('twill.admin_route_name_prefix') . "$routePrefix.$featuredSectionKey.save"),
        ]);
    }

    /**
     * @param array $featuredSection
     * @param string $featuredSectionKey
     * @return array
     */
    private function getFeaturedItemsByBucket($featuredSection, $featuredSectionKey)
    {
        $bucketRouteConfig = $this->config->get('twill.bucketsRoutes') ?? [$featuredSectionKey => 'featured'];
        return Collection::make($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use ($featuredSectionKey, $bucketRouteConfig) {
            $routePrefix = $bucketRouteConfig[$featuredSectionKey];
            return [
                'id' => $bucketKey,
                'name' => $bucket['name'],
                'max' => $bucket['max_items'],
                'acceptedSources' => Collection::make($bucket['bucketables'])->pluck('module'),
                'withToggleFeatured' => $bucket['with_starred_items'] ?? false,
                'toggleFeaturedLabels' => $bucket['starred_items_labels'] ?? [],
                'children' => Feature::where('bucket_key', $bucketKey)->with('featured')->get()->map(function ($feature) use ($bucket) {
                    if (($item = $feature->featured) != null) {
                        $forModuleRepository = collect($bucket['bucketables'])->where('module', $feature->featured_type)->first()['repository'] ?? null;
                        $repository = $this->getRepository($feature->featured_type, $forModuleRepository);
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
            return Collection::make($bucket['bucketables'])->mapWithKeys(function ($bucketable) use (&$fetchedModules, $search, $request) {

                $module = $bucketable['module'];
                $repository = $this->getRepository($module, $bucketable['repository'] ?? null);
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
                    true
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
            $bucketables->each(function ($bucketableData, $bucketable) use (&$featuredSources) {
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
    public function save(Request $request, DB $db)
    {
        $db->transaction(function () use ($request) {
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

    /**
     * @param string $bucketable
     * @return \A17\Twill\Repositories\ModuleRepository
     */
    private function getRepository($bucketable, $forModule = null)
    {
        return $this->app->make($forModule ?: $this->config->get('twill.namespace') . "\Repositories\\" . ucfirst(Str::singular($bucketable)) . "Repository");
    }
}
