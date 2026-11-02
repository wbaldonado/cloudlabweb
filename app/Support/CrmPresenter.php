<?php

namespace App\Support;

use App\Models\CRM\Activity;
use App\Models\CRM\Company;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;

class CrmPresenter
{
    protected static function path(string $name, mixed $parameters = []): string
    {
        return route($name, $parameters, false);
    }

    public static function contact(Contact $contact): array
    {
        $contact->loadMissing(['company', 'owner']);

        return [
            'id' => $contact->id,
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'full_name' => $contact->full_name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'job_title' => $contact->job_title,
            'lifecycle_stage' => $contact->lifecycle_stage,
            'company_id' => $contact->company_id,
            'company' => $contact->company?->name,
            'owner_id' => $contact->owner_id,
            'notes' => $contact->notes,
            'stage_label' => $contact->stageLabel(),
            'stage_class' => $contact->stageBadgeClass(),
            'update_url' => self::path('admin.crm.contacts.update', $contact),
            'destroy_url' => self::path('admin.crm.contacts.destroy', $contact),
            'send_email_url' => $contact->email
                ? self::path('admin.crm.contacts.email.store', $contact)
                : null,
        ];
    }

    public static function company(Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'industry' => $company->industry,
            'website' => $company->website,
            'phone' => $company->phone,
            'address' => $company->address,
            'city' => $company->city,
            'state' => $company->state,
            'country' => $company->country,
            'zip_code' => $company->zip_code,
            'description' => $company->description,
            'meta' => trim(($company->industry ?? 'Sin industria') . ' · ' . ($company->country ?? '—'), ' ·'),
            'update_url' => self::path('admin.crm.companies.update', $company),
            'destroy_url' => self::path('admin.crm.companies.destroy', $company),
        ];
    }

    public static function deal(Deal $deal): array
    {
        $deal->loadMissing(['company', 'contact', 'owner']);

        return [
            'id' => $deal->id,
            'title' => $deal->title,
            'amount' => (float) $deal->amount,
            'stage' => $deal->stage,
            'contact_id' => $deal->contact_id,
            'company_id' => $deal->company_id,
            'owner_id' => $deal->owner_id,
            'expected_close_date' => $deal->expected_close_date?->format('Y-m-d'),
            'description' => $deal->description,
            'probability' => (int) $deal->probability,
            'stage_label' => $deal->stageLabel(),
            'stage_class' => $deal->stageBadgeClass(),
            'company' => $deal->company?->name,
            'contact' => $deal->contact?->full_name,
            'weighted' => $deal->weightedValue(),
            'update_url' => self::path('admin.crm.deals.update', $deal),
            'destroy_url' => self::path('admin.crm.deals.destroy', $deal),
        ];
    }

    public static function activity(Activity $activity): array
    {
        $activity->loadMissing(['company', 'contact']);

        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'type' => $activity->type,
            'status' => $activity->status,
            'due_date' => $activity->due_date?->format('Y-m-d'),
            'contact_id' => $activity->contact_id,
            'company_id' => $activity->company_id,
            'deal_id' => $activity->deal_id,
            'owner_id' => $activity->owner_id,
            'notes' => $activity->notes,
            'type_label' => $activity->typeLabel(),
            'status_label' => $activity->statusLabel(),
            'meta' => ($activity->company?->name ?? $activity->contact?->full_name ?? '—'),
            'update_url' => self::path('admin.crm.activities.update', $activity),
            'destroy_url' => self::path('admin.crm.activities.destroy', $activity),
        ];
    }
}
