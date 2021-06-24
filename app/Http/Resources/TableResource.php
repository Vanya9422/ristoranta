<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed title
 * @property mixed id
 * @property mixed number
 * @property mixed status
 * @property mixed seats
 * @property mixed business_id
 * @property mixed qrcode
 */
class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'title' => $this->title,
            'number' => $this->number,
            'status' => $this->status,
            'seats' => $this->seats,
            'business_id' => Hashids::encode($this->business_id),
            'qrcode' => asset($this->qrcode->url),
            'waiter' => new UserResource($this->whenLoaded('waiter')),
            'manager' => new UserResource($this->whenLoaded('manager')),
        ];
    }
}
