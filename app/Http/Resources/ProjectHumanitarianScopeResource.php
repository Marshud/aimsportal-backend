<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectHumanitarianScopeResource extends JsonResource
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
            'iati_code' => $this->code,
            'iati_vocabulary' => $this->vocabulary,
            'iati_vocabulary_uri' => $this->vocabulary_uri,
            'vocabulary' => $this->iati_vocabulary()->name ?? null,
            'type' => $this->iati_type()->name ?? null,
            'audits' => (can_see_audits($this->resource)) ? AuditResource::collection($this->whenLoaded('audits')) : '',
        ];
    }


}
