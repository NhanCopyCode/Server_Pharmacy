<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'main_image' => $this->main_image,
            'description' => $this->description,
            'inventory' => $this->inventory,
            'price' => $this->price,
            'isDeleted' => $this->isDeleted,
            'brandId' => $this->brandId,
            'categoryId' => $this->categoryId,
            'brandName' => optional($this->brand)->name,
            'categoryName' => optional($this->category)->name,
            'outstanding' => $this->outstanding,
            'approved' => $this->approved,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                ];
            }),
        ];
    }
}
