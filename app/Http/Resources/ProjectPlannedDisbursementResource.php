<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectPlannedDisbursementResource extends JsonResource
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
            'iati_period_start_iso_date' => $this->period_start,
            'iati_period_end_iso_date' => $this->period_end,
            'iati_value_currency' => $this->value_currency,
            'iati_value_date' => $this->value_date,
            'iati_value_amount' => $this->value_amount,
            'type' => $this->iati_type()->name ?? null,
            'provider_org' => new ProjectPlannedDisbursementProviderOrgResource($this->provider_org),
            'receiver_org' => new ProjectPlannedDisbursementReceiverOrgResource($this->receiver_org),
            'audits' => (can_see_audits($this->resource)) ? AuditResource::collection($this->whenLoaded('audits')) : '',
        ];
    }
}
