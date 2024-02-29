<?php


namespace App\Http\Controllers\Api;


use A17\Twill\Models\Feature;
use App\Http\Resources\WorkResource;

class HomeController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $features = Feature::with(['featured.medias', 'featured.slugs'])->get()->groupBy('bucket_key')->map(function($items) {
            return $items->map(function ($item) {
                return [
                    'type' => $item->featured_type,
                    'featured' => new WorkResource($item->featured)
                ];
            });
        });

        return response()->json($features);
    }
}
