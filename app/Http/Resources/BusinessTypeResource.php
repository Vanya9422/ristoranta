<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed type
 * @property mixed description
 * @property mixed created_at
 */
class BusinessTypeResource extends JsonResource
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
            'type' => $this->type,
            'description' => $this->when($this->description, $this->description),
            'created_at' => $this->when($this->created_at, $this->created_at),
        ];
    }
}
