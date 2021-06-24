<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed title
 * @property mixed created_at
 * @property mixed address
 * @property mixed phone
 */
class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'title' => $this->title,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'type' => new BusinessTypeResource($this->whenLoaded('type')),
            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => new BusinessResource($this->whenLoaded('parent')),
            'branches' => BusinessResource::collection($this->whenLoaded('branches')),
            'tables' => TableResource::collection($this->whenLoaded('tables')),
            'dishes' => MenuCollection::collection($this->whenLoaded('dishes')),
            'workers' => UserResource::collection($this->whenLoaded('workers')),
        ];
    }
}
