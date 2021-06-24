<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed color
 */
class TagResource extends JsonResource
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
            'name' => $this->name,
            'color' => $this->when($this->color, $this->color),
        ];
    }
}
