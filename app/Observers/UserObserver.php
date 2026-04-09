<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->moodle_id) {
            return;
        }

        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'CREATE',
            'entity_type' => 'users',
            'entity_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    public function updated(User $user): void
    {
        if (!$user->moodle_id) {
            return;
        }

        $changed = $user->getChanges();
        $syncColumns = ['moodle_id', 'sync_status', 'sync_action', 'synced_at', 'dirty', 'updated_at'];
        $businessChanges = collect($changed)->reject(function ($value, $key) use ($syncColumns) {
            return in_array($key, $syncColumns);
        })->count();

        if ($businessChanges > 0) {
            DB::table('sync_queue')->insertOrIgnore([
                'operation' => 'UPDATE',
                'entity_type' => 'users',
                'entity_id' => $user->id,
                'payload' => json_encode($user->toArray()),
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }

    public function deleting(User $user): void
    {
        if ($user->moodle_id) {
            DB::table('sync_queue')->insertOrIgnore([
                'operation' => 'DELETE',
                'entity_type' => 'users',
                'entity_id' => $user->id,
                'payload' => json_encode($user->toArray()),
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }
}



