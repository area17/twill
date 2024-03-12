<?php

namespace A17\Twill\Http\Controllers\Admin;

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

        $featuredSources = $this->getFeaturedSources($request, $featuredSection, $filters['search'] ?? '', $request->get('content_type'));

        $contentTypes = Collection::make($featuredSources)->map(function ($source, $sourceKey) {
            return [
                'label' => $source['name'],
                'value' => $sourceKey,
                'type' => $source['type'],
            ];
        })->values()->all();

        $firstSource = Arr::first($featuredSources);

        if ($request->has('content_type')) {
            $source = $firstSource;

            return [
                'source' => [
                    'items' => $source['items'],
                ],
                'maxPage' => $source['maxPage'],
            ];
        }

        $buckets = $this->getFeaturedItemsByBucket($featuredSection);

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
     * @return array
     */
    private function getFeaturedItemsByBucket($featuredSection)
    {
        return collect($featuredSection['buckets'])->map(function ($bucket, $bucketKey) {
            return [
                'id' => $bucketKey,
                'name' => $bucket['name'],
                'max' => $bucket['max_items'],
                'acceptedSources' => Collection::make($bucket['bucketables'])->pluck('module'),
                'withToggleFeatured' => $bucket['with_starred_items'] ?? false,
                'toggleFeaturedLabels' => $bucket['starred_items_labels'] ?? [],
                'children' => Feature::where('bucket_key', $bucketKey)->with('featured')->get()->map(function ($feature) {
                    if (($item = $feature->featured) != null) {
                        $repository = getModelRepository($item);
                        $withImage = classHasTrait($repository, HandleMedias::class);

                        return [
                                'id' => $item->id,
                                'name' => $item->titleInBucket ?? $item->title,
                                'edit' => $item->adminEditUrl ?? '',
                                'starred' => $feature->starred ?? false,
                                'type' => $feature->featured_type,
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
    private function getFeaturedSources(Request $request, $featuredSection, string $search = null, string $contentType = null)
    {
        $featuredSources = [];

        foreach ($featuredSection['buckets'] as $bucket) {
            foreach ($bucket['bucketables'] as $bucketable) {
                $module = $bucketable['module'];

                if ((!empty($contentType) && $module !== $contentType) || isset($featuredSources[$module])) {
                    continue;
                }

                $repository = $this->getRepository($module, $bucketable['repository'] ?? null);
                $translated = classHasTrait($repository, HandleTranslations::class);
                $withImage = classHasTrait($repository, HandleMedias::class);

                if ($search) {
                    $searchField = $bucketable['searchField'] ?? ($translated ? 'title' : '%title');
                    $scopes[$searchField] = $search;
                }

                $items = null;
                if (!empty($contentType) || empty($featuredSources)) {
                    $items = $repository->get(
                        $bucketable['with'] ?? [],
                        ($bucketable['scopes'] ?? []) + ($scopes ?? []),
                        $bucketable['orders'] ?? [],
                        $bucketable['per_page'] ?? $request->get('offset') ?? 10,
                        true
                    )->appends('bucketable', $module);
                }

                $morphClass = $repository->getBaseModel()->getMorphClass();

                $featuredSources[$module] = [
                    'name' => $bucketable['name'] ?? ucfirst($module),
                    'type' => $morphClass,
                    'maxPage' => $items?->lastPage(),
                    'offset' => $items?->perPage(),
                    'items' => $items?->map(function ($item) use ($morphClass, $translated, $withImage) {
                        return [
                                'id' => $item->id,
                                'name' => $item->titleInBucket ?? $item->title,
                                'edit' => $item->adminEditUrl ?? '',
                                'type' => $morphClass,
                            ] + ($translated ? [
                                'languages' => $item->getActiveLanguages(),
                            ] : []) + ($withImage ? [
                                'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
                            ] : []);
                    })->all(),
                ];
                if ($contentType === $module) {
                    break 2;
                }
            }
        }

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
