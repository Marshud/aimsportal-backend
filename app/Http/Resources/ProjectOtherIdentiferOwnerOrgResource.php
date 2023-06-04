<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectOtherIdentiferOwnerOrgResource extends JsonResource
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
            'iati_ref' => $this->ref,
            'narratives' => ProjectNarrativeResource::collection($this->narratives),
            'audits' => (can_see_audits($this->resource)) ? AuditResource::collection($this->whenLoaded('audits')) : '',
        ];
    }

    
}
