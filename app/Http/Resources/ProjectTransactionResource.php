<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTransactionResource extends JsonResource
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
            'iati_humanitarian' => $this->humanitarian,
            'iati_transaction_type_code' => $this->transaction_type_code,
            'iati_transaction_date' => $this->transaction_date,
            'iati_value_currency' => $this->value_currency,
            'iati_value_date' => $this->value_date,
            'iati_value_amount' => $this->value_amount,
            'iati_disbursement_channel_code' => $this->disbursement_channel_code,
            'iati_recipient_country_code' => $this->recipient_country_code,
            'iati_recipient_region_code' => $this->recipient_region_code,
            'iati_recipient_region_vocabulary' => $this->recipient_region_vocabulary,
            'iati_flow_type_code' => $this->flow_type_code,
            'iati_finance_type_code' => $this->finance_type_code,
            'iati_tied_status_code' => $this->tied_status_code,
            'sectors' => ProjectTransactionSectorResource::collection($this->sectors),
            'provider_org' => new ProjectTransactionProviderOrgResource($this->provider_org),
            'receiver_org' => new ProjectTransactionReceiverOrgResource($this->receiver_org),
        ];
    }
}
