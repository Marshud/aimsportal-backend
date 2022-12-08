<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectNarrativeResource extends JsonResource
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
            'narrative' => $this->narrative,
            'lang' => $this->lang,
        ];
    }

}
