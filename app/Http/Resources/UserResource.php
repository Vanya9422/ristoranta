<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed id
 * @property mixed first_name
 * @property mixed middle_name
 * @property mixed last_name
 * @property mixed email
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed uid
 * @property mixed birthday
 * @property mixed phone
 * @property mixed address
 * @property mixed roles
 * @property mixed chat_id
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if (!$this->resource) return [];

        return [
            'id' => Hashids::encode($this->id),
            'chat_id' => $this->when($this->chat_id, Hashids::encode($this->chat_id)),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->when($this->middle_name, $this->middle_name),
            'birthday' => $this->when($this->birthday, $this->birthday),
            'phone' => $this->when($this->phone, $this->phone),
            'email' => $this->when($this->email, $this->email),
            'address' => $this->when($this->address, $this->address),
            'created_at' => $this->when($this->created_at, $this->created_at),
            'updated_at' => $this->when($this->updated_at, $this->updated_at),
            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),
            'business' => BusinessResource::collection(
                $this->whenLoaded('business')
            ),
            'tables' => TableResource::collection(
                $this->whenLoaded('tables')
            ),
            'workers' => UserResource::collection(
                $this->whenLoaded('workers')
            ),
        ];
    }
}
