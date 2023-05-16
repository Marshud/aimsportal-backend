<?php

namespace App\Http\Resources;

use App\Enums\CoreRoles;
use App\Models\ProjectParticipatingOrg;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'default_title' => $this->title,
            'iati_identifier' => $this->iati_identifier,
            'iati_activity_status' => $this->activity_status,
            'iati_activity_scope' => $this->activity_scope,
            'iati_country_budget_items_vocabulary' => $this->country_budget_items_vocabulary,
            'iati_colaboration_type_code' => $this->colaboration_type_code,
            'iati_default_flow_type_code' => $this->default_flow_type_code,
            'iati_default_finance_type_code' => $this->default_finance_type_code,
            'iati_default_tied_status' => $this->default_tied_status,
            'iati_capital_spend_percentage' => $this->capital_spend_percentage,
            'iati_conditions_attached' => $this->conditions_attached,
            'iati_activity_status' => iati_get_code_value('ActivityStatus', $this->activity_status),
            'status' => $this->status,
            'activity_status' => $this->activity_status,
            'title' => ProjectNarrativeResource::collection($this->whenLoaded('title_translations')),
            'reporting_org' => new OrganisationResource($this->whenLoaded('reporting_organisation')),
            'humanitarian_scope' => ProjectHumanitarianScopeResource::collection($this->whenLoaded('humanitarian_scopes')),
            'participating_org' => ProjectParticipatingOrgResource::collection($this->whenLoaded('participating_organisations')),
            'description' => ProjectDescriptionResource::collection($this->whenLoaded('project_descriptions')),
            'other_identifiers' => ProjectOtherIdentiferResource::collection($this->whenLoaded('other_identifiers')),
            'activity_date' => ProjectActivityDateResource::collection($this->whenLoaded('activity_dates')),
            'recipient_country' => ProjectRecipientCountryResource::collection($this->whenLoaded('recipient_countries')),
            'recipient_region' => ProjectRecipientRegionResource::collection($this->whenLoaded('recipient_regions')),
            'location' => ProjectLocationResource::collection($this->whenLoaded('locations')),
            'sector' => ProjectSectorResource::collection($this->whenLoaded('sectors')),
            'tag' => ProjectTagResource::collection($this->whenLoaded('tags')),
            'budget_item' => ProjectCountryBudgetItemResouce::collection($this->whenLoaded('country_budget_items')),
            'policy_marker' => ProjectPolicyMarkerResource::collection($this->whenLoaded('policy_markers')),
            'default_aid_type' => ProjectDefaultAidTypeResource::collection($this->whenLoaded('default_aid_types')),
            'budget' => ProjectBudgetResource::collection($this->whenLoaded('budgets')),
            'planned_disbursement' => ProjectPlannedDisbursementResource::collection($this->whenLoaded('planned_disbursements')),
            'transaction' => ProjectTransactionResource::collection($this->whenLoaded('transactions')),
            'audits' => (can_see_audits($this->resource)) ? AuditResource::collection($this->whenLoaded('audits')) : '',
            'editable' => can_edit_project($this->resource),
            'auditable' => can_see_audits($this->resource),
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
