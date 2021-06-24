<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed regional
 * @property mixed code
 * @property mixed native
 * @property mixed name
 * @property mixed id
 */
class LanguageResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'native' => $this->native,
            'code' => $this->code,
            'regional' => $this->when($this->regional, $this->regional),
        ];
    }
}
