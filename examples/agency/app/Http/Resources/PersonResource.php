<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'biography' => $this->biography,
            'image' => $this->imagesAsArrays('main'),
            'slug' => $this->slug,
            'office' => $this->office_name,
            'videos' => PersonVideoResource::collection($this->whenLoaded('videos')),
            'publications' => WorkResource::collection($this->whenLoaded('works'))
        ];
    }
}
