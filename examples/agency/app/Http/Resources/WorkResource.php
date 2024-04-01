<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'cover' => $this->imagesAsArraysWithCrops('cover'),
            'homepage_image' => $this->imagesAsArraysWithCrops('homepage_slideshow'),
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'case_study_text' => $this->case_study_text,
            'video_url' => $this->video_url,
            'autoplay' => $this->autoplay,
            'autoloop' => $this->autoloop,
            'blocks' => BlockResource::collection($this->whenLoaded('blocks')),
            'year' => $this->year
        ];
    }
}
