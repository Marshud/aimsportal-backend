<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectPolicyMarkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'iati_code' => $this->code,
            'iati_percentage' => $this->percentage,
            'iati_vocabulary' => $this->vocabulary,
            'iati_vocabulary_uri' => $this->vocabulary_uri,
            'iati_significance' => $this->significance,
            'code' => $this->iati_code()->name ?? null,
            'vocabulary' => $this->iati_vocabulary()->name ?? null,
            'significance' => $this->iati_significance()->name ?? null,
            'narratives' => ProjectNarrativeResource::collection($this->narratives),
        ];
    }
}
