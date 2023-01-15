<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'field_type' => $this->field_type,
            'required' => $this->required,
            'options' => $this->options,
            'audits' => ($this->canSeeAudits()) ? $this->audits : '',
        ];
    }

    private function canSeeAudits(): bool
    {
        if (auth('sanctum')->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return true;
        }

        return false;
    }
}
