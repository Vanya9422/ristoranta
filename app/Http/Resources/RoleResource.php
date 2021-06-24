<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed display_name
 * @property mixed name
 */
class RoleResource extends JsonResource
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
            'display_name' => $this->display_name
        ];
    }
}
