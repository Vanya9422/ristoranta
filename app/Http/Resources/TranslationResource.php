<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed translations
 * @property mixed id
 * @property mixed section
 * @property mixed language
 */
class TranslationResource extends JsonResource
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
            'translations' => $this->when($this->translations, $this->translations),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'section' => $this->whenLoaded('section', function () {
                return $this->section->name;
            }),
        ];
    }
}
