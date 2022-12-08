<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTransactionProviderOrgResource extends JsonResource
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
            'iati_ref' => $this->ref,
            'iati_provider_activity_id' => $this->provider_activity_id,
            'type' => $this->iati_type()->name ?? null,
        ];
    }
}
