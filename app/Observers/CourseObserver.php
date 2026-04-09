<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseObserver
{
    /**
     * Après création d'un course, enqueue CREATE si pas de moodle_id
     */
    public function created(Course $course): void
    {
        // Si pas de moodle_id = créé localement
        if (!$course->moodle_id) {
            // Vérifier d'abord si l'opération n'existe pas déjà
            $exists = DB::table('sync_queue')
                ->where('entity_type', 'courses')
                ->where('entity_id', $course->id)
                ->where('operation', 'CREATE')
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                DB::table('sync_queue')->insert([
                    'operation' => 'CREATE',
                    'entity_type' => 'courses',
                    'entity_id' => $course->id,
                    'payload' => json_encode($course->toArray()),
                    'status' => 'pending',
                    'created_at' => now(),
                ]);

                Log::info("Course créé: ID={$course->id}, enqueued");
            } else {
                Log::info("Course créé: ID={$course->id}, déjà en queue");
            }

            DB::table('courses')
                ->where('id', $course->id)
                ->update([
                    'sync_status' => 'pending',
                    'dirty' => 1,
                ]);
        }
    }

    /**
     * Après mise à jour d'un course
     */
    public function updated(Course $course): void
    {
        $changed = $course->getChanges();
        
        // Ignorer les changements de colonnes sync
        $nonSyncChanges = collect($changed)->reject(function($value, $key) {
            return in_array($key, ['sync_status', 'sync_action', 'synced_at', 'dirty', 'updated_at']);
        })->count();

        // Si c'est un course Moodle (a un moodle_id) et il a été modifié en dehors de sync
        if ($nonSyncChanges > 0 && $course->moodle_id) {
            // Vérifier si l'UPDATE existe déjà
            $exists = DB::table('sync_queue')
                ->where('entity_type', 'courses')
                ->where('entity_id', $course->id)
                ->where('operation', 'UPDATE')
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                // Enqueue UPDATE
                DB::table('sync_queue')->insert([
                    'operation' => 'UPDATE',
                    'entity_type' => 'courses',
                    'entity_id' => $course->id,
                    'payload' => json_encode($course->toArray()),
                    'status' => 'pending',
                    'created_at' => now(),
                ]);

                Log::info("Course modifié: ID={$course->id}, enqueued pour sync");
            } else {
                Log::info("Course modifié: ID={$course->id}, UPDATE déjà en queue");
            }

            // Marquer comme pending pour push
            DB::table('courses')
                ->where('id', $course->id)
                ->update([
                    'sync_status' => 'pending',
                    'dirty' => 1,
                ]);
        }
    }

    /**
     * Avant suppression d'un course
     */
    public function deleting(Course $course): void
    {
        if ($course->moodle_id) {
            // Vérifier si le DELETE existe déjà
            $exists = DB::table('sync_queue')
                ->where('entity_type', 'courses')
                ->where('entity_id', $course->id)
                ->where('operation', 'DELETE')
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                DB::table('sync_queue')->insert([
                    'operation' => 'DELETE',
                    'entity_type' => 'courses',
                    'entity_id' => $course->id,
                    'payload' => json_encode($course->toArray()),
                    'status' => 'pending',
                    'created_at' => now(),
                ]);

                Log::info("Course supprimé: ID={$course->id}, enqueued pour DELETE");
            } else {
                Log::info("Course supprimé: ID={$course->id}, DELETE déjà en queue");
            }
        }
    }
}
