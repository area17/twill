<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Models\Feature;
use App\Http\Controllers\Controller;
use DB;

class FeaturedController extends Controller
{
    public function index()
    {
        $featuredSectionKey = request()->segment(count(request()->segments()));
        $featuredSection = config("cms-toolkit.buckets.$featuredSectionKey");

        if (request()->has("search_" . request('bucketable'))) {
            $bucketable = request('bucketable');
            $featurableItemsByBucketable = $this->getFeaturableItemsByBucketable($featuredSection, request("search_{$bucketable}"));

            return [
                'bucketableName' => $featurableItemsByBucketable[$bucketable]['name'] ?? [],
                'items' => $featurableItemsByBucketable[$bucketable]['items'] ?? [],
                'buckets' => $featurableItemsByBucketable[$bucketable]['buckets'] ?? [],
                'bucketable' => $bucketable,
                'all_buckets' => collect($featuredSection['buckets']),
                'search' => request("search_{$bucketable}") ?? null,
            ];
        }

        $featurableItemsByBucketable = $this->getFeaturableItemsByBucketable($featuredSection);
        $featuredItemsByBucket = $this->getFeaturedItemsByBucket($featuredSection);

        $this->prepareSessionWithCurrentFeatures($featuredItemsByBucket);

        return [
            'featurableItemsByBucketable' => $featurableItemsByBucketable,
            'featuredItemsByBucket' => $featuredItemsByBucket,
            'buckets' => collect($featuredSection['buckets']),
            'sectionKey' => $featuredSectionKey,
        ];
    }

    private function prepareSessionWithCurrentFeatures($featuredItemsByBucket)
    {
        session()->forget('buckets');
        collect($featuredItemsByBucket)->each(function ($items, $bucket) {
            $items->each(function ($item) use ($bucket) {
                session()->push("buckets.$bucket", [
                    'id' => $item->featured_id,
                    'type' => $item->featured_type,
                ]);
            });
        });
    }

    public function add($bucket)
    {
        session()->push("buckets.$bucket", request()->all());
        $this->save();
        return response()->json();
    }

    public function remove($bucket)
    {
        $currentBucket = session()->get("buckets.$bucket");

        collect($currentBucket)->each(function ($bucketItem, $index) use (&$currentBucket) {
            if ($bucketItem['id'] === request('id') && $bucketItem['type'] === request('type')) {
                unset($currentBucket[$index]);
            }
        });

        session()->put("buckets.$bucket", $currentBucket);
        $this->save();
        return response()->json();
    }

    public function sortable($bucket)
    {
        if ($bucket != null && ($values = json_decode(request()->getContent(), true)) && !empty($values)) {
            session()->put("buckets.$bucket", $values);
            $this->save();
        }
    }

    public function save()
    {
        DB::transaction(function () {
            collect(session()->get('buckets'))->each(function ($bucketables, $bucketKey) {
                Feature::where('bucket_key', $bucketKey)->delete();
                foreach (($bucketables ?? []) as $position => $bucketable) {
                    Feature::create([
                        'featured_id' => $bucketable['id'],
                        'featured_type' => $bucketable['type'],
                        'position' => $position + 1,
                        'bucket_key' => $bucketKey,
                    ]);
                }
            });
        });
        \Event::fire('buckets.saved');

        return redirect()->back();
    }

    public function cancel()
    {
        return redirect()->back();
    }

    private function getFeaturedItemsByBucket($featuredSection)
    {
        $featuredItems = [];
        collect($featuredSection['buckets'])->each(function ($bucket, $bucketKey) use (&$featuredItems) {
            $featuredItems[$bucketKey] = Feature::where('bucket_key', $bucketKey)->with('featured')->get();
        });

        return $featuredItems;
    }

    private function getFeaturableItemsByBucketable($featuredSection, $search = null)
    {
        $fetchedBucketables = [];
        $featurableItemsByBucketable = [];

        collect($featuredSection['buckets'])->map(function ($bucket, $bucketKey) use (&$fetchedBucketables, $search) {
            return collect($bucket['bucketables'])->mapWithKeys(function ($bucketable) use (&$fetchedBucketables, $bucketKey, $search) {

                $module = $bucketable['module'];

                if ($search) {
                    $searchField = $bucketable['search_field'] ?? '%title';
                    $scopes[$searchField] = $search;
                }

                $items = $fetchedBucketables[$module] ?? $this->getRepository($module)->get(
                    $bucketable['with'] ?? [],
                    ($bucketable['scopes'] ?? []) + ($scopes ?? []),
                    $bucketable['orders'] ?? [],
                    $bucketable['per_page'] ?? 5,
                    $forcePagination = true
                )->appends('bucketable', $module);

                $fetchedBucketables[$module] = $items;

                return [$module => [
                    'name' => $bucketable['name'] ?? ucfirst($module),
                    'items' => $items,
                ]];
            });
        })->each(function ($bucketables, $bucket) use (&$featurableItemsByBucketable) {
            $bucketables->each(function ($bucketableData, $bucketable) use ($bucket, &$featurableItemsByBucketable) {
                $featurableItemsByBucketable[$bucketable]['buckets'][] = $bucket;
                $featurableItemsByBucketable[$bucketable]['items'] = $bucketableData['items'];
                $featurableItemsByBucketable[$bucketable]['name'] = $bucketableData['name'];
            });

        });

        return $featurableItemsByBucketable;
    }

    private function getRepository($bucketable)
    {
        return app(config('cms-toolkit.namespace') . "\Repositories\\" . ucfirst(str_singular($bucketable)) . "Repository");
    }

}
