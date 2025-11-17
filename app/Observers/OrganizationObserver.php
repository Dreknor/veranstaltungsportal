<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Organization;

class OrganizationObserver
{
    public function created(Organization $organization): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => Organization::class,
            'auditable_id' => $organization->id,
            'action' => 'created',
            'old_values' => null,
            'new_values' => $organization->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function updated(Organization $organization): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => Organization::class,
            'auditable_id' => $organization->id,
            'action' => 'updated',
            'old_values' => $organization->getOriginal(),
            'new_values' => $organization->getChanges(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function deleted(Organization $organization): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => Organization::class,
            'auditable_id' => $organization->id,
            'action' => 'deleted',
            'old_values' => $organization->toArray(),
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

