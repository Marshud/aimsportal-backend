<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectActivityDateResource extends JsonResource
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
            'iati_type' => $this->type,
            'type' => $this->iati_type()->name ?? null,
            'iati_iso_date' => $this->iso_date,
            'narratives' => ProjectNarrativeResource::collection($this->narratives),
        ];
    }

}
