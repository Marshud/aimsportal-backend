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
            'title' => $this->title,
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
            'reporting_org' => new OrganisationResource($this->reporting_organisation),
            'humanitarian_scope' => ProjectHumanitarianScopeResource::collection($this->humanitarian_scopes),
            'participating_org' => ProjectParticipatingOrgResource::collection($this->participating_organisations),
            'description' => ProjectDescriptionResource::collection($this->project_descriptions),
            'other_identifiers' => ProjectOtherIdentiferResource::collection($this->other_identifiers),
            'activity_date' => ProjectActivityDateResource::collection($this->activity_dates),
            'recipient_country' => ProjectRecipientCountryResource::collection($this->recipient_countries),
            'recipient_region' => ProjectRecipientRegionResource::collection($this->recipient_regions),
            'location' => ProjectLocationResource::collection($this->locations),
            'sector' => ProjectSectorResource::collection($this->sectors),
            'tag' => ProjectTagResource::collection($this->tags),
            'budget_item' => ProjectCountryBudgetItemResouce::collection($this->country_budget_items),
            'policy_marker' => ProjectPolicyMarkerResource::collection($this->policy_markers),
            'default_aid_type' => ProjectDefaultAidTypeResource::collection($this->default_aid_types),
            'budget' => ProjectBudgetResource::collection($this->budgets),
            'planned_disbursement' => ProjectPlannedDisbursementResource::collection($this->planned_disbursements),
            'transaction' => ProjectTransactionResource::collection($this->transactions),
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
