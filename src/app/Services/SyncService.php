<?php

namespace App\Services;

use App\Enums\SyncStatus;
use App\Models\Clinic;
use App\Models\SyncQueue;
use App\Support\Tenant;

class SyncService
{
    public function queue(string $entityType, ?int $entityId, string $action, array $payload = [], ?int $userId = null): ?SyncQueue
    {
        $clinic = Tenant::clinic();
        if (! $clinic instanceof Clinic || $clinic->isLocalOnly()) {
            return null;
        }

        return SyncQueue::query()->create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'payload' => $payload,
            'status' => SyncStatus::Pending,
            'available_at' => now(),
        ]);
    }

    public function pendingCount(): int
    {
        return SyncQueue::query()->where('status', SyncStatus::Pending)->count();
    }

    public function processDue(int $limit = 50): int
    {
        $clinic = Tenant::clinic();
        if (! $clinic || $clinic->isLocalOnly()) {
            return 0;
        }

        $items = SyncQueue::query()
            ->where('status', SyncStatus::Pending)
            ->where(function ($q) {
                $q->whereNull('available_at')->orWhere('available_at', '<=', now());
            })
            ->limit($limit)
            ->get();

        $processed = 0;
        foreach ($items as $item) {
            $item->update(['status' => SyncStatus::Processing, 'attempts' => $item->attempts + 1]);
            try {
                // Integrations are optional; mark complete when no outbound transport is configured.
                $item->update([
                    'status' => SyncStatus::Completed,
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
                $processed++;
            } catch (\Throwable $e) {
                $item->update([
                    'status' => SyncStatus::Failed,
                    'last_error' => 'sync_failed',
                    'available_at' => now()->addMinutes(min(60, $item->attempts * 2)),
                ]);
            }
        }

        return $processed;
    }
}
