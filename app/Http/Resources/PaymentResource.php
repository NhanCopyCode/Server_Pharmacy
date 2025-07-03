<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'orderId' => $this->orderId,
            'payment_gateway' => $this->payment_gateway,
            'transaction_code' => $this->transaction_code,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_time' => $this->payment_time,
            'vnp_response_code' => $this->vnp_response_code,
            'bank_code' => $this->bank_code,
            'note' => $this->note,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
