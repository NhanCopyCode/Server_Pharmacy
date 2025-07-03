<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount_value' => $this->max_discount_value,
            'min_order_value' => $this->min_order_value,
            'applies_to' => $this->applies_to,
            'approved' => $this->approved,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
