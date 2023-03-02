<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectLocationResource extends JsonResource
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
            'location_details' => $this->location_details,
            'state' => $this->state_id,
            'state_name' => $this->state->name ?? null,
            'county' => $this->county_id,
            'county_name' => $this->county->name ?? null,
            'payam' => $this->payam_id,
            'payam_name' => $this->payam->name ?? null,
        ];
    }
}
