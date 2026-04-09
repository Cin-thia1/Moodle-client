<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseObserver
{
    public function created(Course $course): void
    {
        // Ignorer si c'est du serveur (a un moodle_id)
        if ($course->moodle_id) {
            return;
        }

        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'CREATE',
            'entity_type' => 'courses',
            'entity_id' => $course->id,
            'payload' => json_encode($course->toArray()),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    public function updated(Course $course): void
    {
        // Ignorer si c'est local (pas de moodle_id)
        if (!$course->moodle_id) {
            return;
        }

        $changed = $course->getChanges();
        $syncColumns = ['moodle_id', 'sync_status', 'sync_action', 'synced_at', 'dirty', 'updated_at'];
        $businessChanges = collect($changed)->reject(function ($value, $key) use ($syncColumns) {
            return in_array($key, $syncColumns);
        })->count();

        if ($businessChanges > 0) {
            DB::table('sync_queue')->insertOrIgnore([
                'operation' => 'UPDATE',
                'entity_type' => 'courses',
                'entity_id' => $course->id,
                'payload' => json_encode($course->toArray()),
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }

    public function deleting(Course $course): void
    {
        if ($course->moodle_id) {
            DB::table('sync_queue')->insertOrIgnore([
                'operation' => 'DELETE',
                'entity_type' => 'courses',
                'entity_id' => $course->id,
                'payload' => json_encode($course->toArray()),
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }
}



