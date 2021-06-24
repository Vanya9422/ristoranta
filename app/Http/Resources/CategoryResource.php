<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed deleted_at
 */
class CategoryResource extends JsonResource
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
            'created_at' => $this->when($this->created_at, $this->created_at),
            'updated_at' => $this->when($this->updated_at, $this->updated_at),
            'deleted_at' => $this->when($this->deleted_at, $this->deleted_at),
            'subCategories' => CategoryResource::collection($this->whenLoaded('subCategory'))
        ];
    }
}
