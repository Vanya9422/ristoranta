<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed business_id
 * @property mixed updated_at
 * @property mixed created_at
 * @property mixed image
 * @property mixed name
 * @property mixed id
 * @property mixed description
 * @property mixed price
 * @property mixed carbohydrates
 * @property mixed fats
 * @property mixed proteins
 * @property mixed calories
 * @property mixed weight
 * @property mixed category_id
 * @property mixed category
 * @property mixed business
 */
class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            return parent::toArray($request);
        }

        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'weight' => $this->weight,
            'calories' => $this->calories,
            'proteins' => $this->proteins,
            'fats' => $this->fats,
            'carbohydrates' => $this->carbohydrates,
            'business_id' => Hashids::encode($this->business_id),
            'category_id' => Hashids::encode($this->category_id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $this->whenLoaded('image', function () {
                return asset($this->image->url);
            }),
            'category' => $this->whenLoaded('category', function () {
                return $this->category->name;
            }),
            'business' => $this->whenLoaded('business', function () {
                return $this->business->title;
            }),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
