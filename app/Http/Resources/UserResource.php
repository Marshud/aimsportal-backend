<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'status' => $this->status,
            'email' => $this->email,
            'language' => $this->language,
            'organisation' => ($this->current_organisation_id) ? new OrganisationResource($this->currentOrganisation) : null,
            'roles' => RolesResource::collection($this->roles)

        ];
    }
}
