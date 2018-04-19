<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Models\Feature;
use A17\CmsToolkit\Repositories\Behaviors\HandleTranslations;
use App\Http\Controllers\Controller;
use DB;
use Event;

class FeaturedController extends Controller
{
    public function index()
    {
        $featuredSectionKey = request()->segment(count(request()->segments()));
        $featuredSection = config("cms-toolkit.buckets.$featuredSectionKey");
        $filters = json_decode(request()->get('filter'), true) ?? [];

        $featuredSources = $this->getFeaturedSources($featuredSection, $filters['search'] ?? '');

        $contentTypes = collect($featuredSources)->map(function ($source, $sourceKey) {
            return [
                'label' => $source['name'],
                'value' => $sourceKey,
            ];
        })->values()->toArray();

        if (request()->has('content_type')) {
            $source = array_first($featuredSources, function ($source, $sourceKey) {
                return $sourceKey == request('content_type');
            });

            return [
                'source' => [
                    'content_type' => array_first($contentTypes, function ($contentTypeItem) {
                        return $contentTypeItem['value'] == request('content_type');
                    }),
                    'items' => $source['items'],
                ],
                'maxPage' => $source['maxPage'],
            ];
        }

        $buckets = $this->getFeaturedItemsByBucket($featuredSection, $featuredSectionKey);
        $firstSource = array_first($featuredSources);

        $routePrefix = 'featured';

        if (isset(config('cms-toolkit.bucketsRoutes'))) {
            $routePrefix =  config('cms-toolkit.bucketsRoutes')[$featuredSectionKey] ?? $routePrefix;
        }

        return view('cms-toolkit::layouts.buckets', [
            'dataSources' => [
                'selected' => array_first($contentTypes),
                'content_types' => $contentTypes,
            ],
            'items' => $buckets,
            'source' => [
                'content_type' => array_first($contentTypes),
                'items' => $firstSource['items'],
            ],
            'maxPage' => $firstSource['maxPage'],
            'offset' => $firstSource['offset'],
            'bucketSourceTitle' => $featuredSection['sourceHeaderTitle'] ?? null,
            'bucketsSectionIntro' => $featuredSection['sectionIntroText'] ?? null,
            'restricted' => $featuredSection['restricted'] ?? true,
            'saveUrl' => route("admin.$routePrefix.$featuredSectionKey.save"),
        ]);
    }

    private function getFeaturedItemsByBucket($featuredSection, $featuredSectionKey)
    {
        $bucketRouteConfig = config('cms-toolkit.bucketsRoutes') ?? [$featuredSectionKey => 'featured'];
        return collect($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use ($featuredSectionKey, $bucketRouteConfig) {
            $routePrefix = $bucketRouteConfig[$featuredSectionKey];
            return [
                'id' => $bucketKey,
                'name' => $bucket['name'],
                'max' => $bucket['max_items'],
                'acceptedSources' => collect($bucket['bucketables'])->pluck('module'),
                'withToggleFeatured' => $bucket['with_starred_items'] ?? false,
                'toggleFeaturedLabels' => $bucket['starred_items_labels'] ?? [],
                'children' => Feature::where('bucket_key', $bucketKey)->with('featured')->get()->map(function ($feature) {
                    if (($item = $feature->featured) != null) {
                        return [
                            'id' => $item->id,
                            'name' => $item->titleInBucket ?? $item->title,
                            'edit' => $item->adminEditUrl ?? '',
                            'starred' => $feature->starred ?? false,
                            'content_type' => [
                                'label' => ucfirst($feature->featured_type),
                                'value' => $feature->featured_type,
                            ],
                        ];
                    }
                })->reject(function ($item) {
                    return is_null($item);
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    private function getFeaturedSources($featuredSection, $search = null)
    {
        $fetchedModules = [];
        $featuredSources = [];

        collect($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use (&$fetchedModules, $search) {
            return collect($bucket['bucketables'])->mapWithKeys(function ($bucketable) use (&$fetchedModules, $bucketKey, $search) {

                $module = $bucketable['module'];
                $repository = $this->getRepository($module);
                $translated = classHasTrait($repository, HandleTranslations::class);

                if ($search) {
                    $searchField = $bucketable['searchField'] ?? ($translated ? 'title' : '%title');
                    $scopes[$searchField] = $search;
                }

                $items = $fetchedModules[$module] ?? $repository->get(
                    $bucketable['with'] ?? [],
                    ($bucketable['scopes'] ?? []) + ($scopes ?? []),
                    $bucketable['orders'] ?? [],
                    $bucketable['per_page'] ?? request('offset') ?? 10,
                    $forcePagination = true
                )->appends('bucketable', $module);

                $fetchedModules[$module] = $items;

                return [$module => [
                    'name' => $bucketable['name'] ?? ucfirst($module),
                    'items' => $items,
                    'translated' => $translated,
                ]];
            });
        })->each(function ($bucketables, $bucket) use (&$featuredSources) {
            $bucketables->each(function ($bucketableData, $bucketable) use ($bucket, &$featuredSources) {
                // $featuredSources[$bucketable]['buckets'][] = $bucket; // not used at the moment because our new components are not supporting restricting items from going into a certain bucket.
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
                    ] + ($bucketableData['translated'] ? ['languages' => $item->getActiveLanguages()] : []);
                })->toArray();
            });

        });

        return $featuredSources;
    }

    public function save()
    {
        DB::transaction(function () {
            collect(request('buckets'))->each(function ($bucketables, $bucketKey) {
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

        Event::fire('cms-buckets.saved', 'cms-buckets.saved');
    }

    private function getRepository($bucketable)
    {
        return app(config('cms-toolkit.namespace') . "\Repositories\\" . ucfirst(str_singular($bucketable)) . "Repository");
    }
}
