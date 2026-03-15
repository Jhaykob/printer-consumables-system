<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function updated($model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']); // Ignore simple timestamp updates

        if (empty($changes)) return;

        $before = array_intersect_key($model->getOriginal(), $changes);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'model_type' => class_basename($model),
            'model_id' => $model->id, // <-- ADD THIS LINE
            'before' => json_encode($before),
            'after' => json_encode($changes),
        ]);
    }

    public function deleted($model): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted (Soft)',
            'model_type' => class_basename($model),
            'model_id' => $model->id, // <-- ADD THIS LINE
            'before' => json_encode($model->getOriginal()),
        ]);
    }
}
