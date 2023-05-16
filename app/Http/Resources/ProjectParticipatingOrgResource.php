<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectParticipatingOrgResource extends JsonResource
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
            'organisation_id' => $this->organisation_id,
            'iati_ref' => $this->organisation->iati_org_id ?? null,
            'iati_type' => $this->type,
            'iati_role' => $this->role,
            'iati_crs_channel_code' => $this->crs_channel_code,
            'type' => $this->iati_type()->name ?? null,
            'role' => $this->iati_role()->name ?? null,
            'crs_channel_code' => $this->iati_crs_channel()->name ?? null,
            'audits' => (can_see_audits($this->resource)) ? AuditResource::collection($this->whenLoaded('audits')) : '',

        ];
    }


}
