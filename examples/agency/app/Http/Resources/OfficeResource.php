<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
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
            'description' => $this->description,
            'street' => $this->street,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'directions' => $this->directions,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'images' => $this->imagesAsArrays('cover')
        ];
    }
}
