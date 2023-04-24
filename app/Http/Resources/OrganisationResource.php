<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationResource extends JsonResource
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
            'name' => $this->name,
            'acronym' => $this->acronym,
            'address' => $this->address,
            'contact_person_name' => $this->contact_person_name,
            'contact_person_email' => $this->contact_person_email,
            'approved' => $this->approved,
            'number_of_users' => $this->whenLoaded('team_users',$this->team_users->count()),
            'category' => $this->category->name ?? 'none',
            'category_id' => $this->category_id,
            'description' => $this->description,
            'iati_org_id' => $this->iati_org_id,
            'iati_org_type' => $this->iati_org_type,
            'type' => $this->iati_type()->name ?? null,
            'country' => $this->country,
            'audits' => ($this->canSeeAudits()) ? $this->audits : '',
        ];
    }

    private function canSeeAudits(): bool
    {
        if (false === auth('sanctum')->check()) {
            return false;
        }

        if (auth('sanctum')->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return true;
        }

        if (auth('sanctum')->user()->hasRole(CoreRoles::Manager->value)) {
            return true;
        }

        return false;
    }
}
