<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->write(
            model: $model,
            event: 'created',
            oldValues: null,
            newValues: $this->clean(
                $model->getAttributes()
            ),
        );
    }

    public function updated(Model $model): void
    {
        $changes =
            $this->clean(
                $model->getChanges()
            );

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach (
            array_keys($changes)
            as $key
        ) {
            $oldValues[$key] =
                $model->getOriginal($key);
        }

        $this->write(
            model: $model,
            event: 'updated',
            oldValues: $oldValues,
            newValues: $changes,
        );
    }

    public function deleted(Model $model): void
    {
        $this->write(
            model: $model,
            event: 'deleted',
            oldValues: $this->clean(
                $model->getAttributes()
            ),
            newValues: null,
        );
    }

    private function write(
        Model $model,
        string $event,
        ?array $oldValues,
        ?array $newValues,
    ): void {
        $request = app()->bound('request')
            ? request()
            : null;

        AuditLog::create([
            'user_id' =>
                Auth::id(),

            'event' =>
                $event,

            'auditable_type' =>
                $model->getMorphClass(),

            'auditable_id' =>
                $model->getKey(),

            'old_values' =>
                $oldValues,

            'new_values' =>
                $newValues,

            'ip_address' =>
                $request?->ip(),

            'user_agent' =>
                $request?->userAgent(),
        ]);
    }

    private function clean(
        array $values
    ): array {
        return Arr::except(
            $values,
            [
                'created_at',
                'updated_at',
            ]
        );
    }
}