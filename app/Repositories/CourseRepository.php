<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des cours avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 */
class CourseRepository
{
    /**
     * Crée un nouveau cours localement et enqueue la sync.
     */
    public function create(array $data): Course
    {
        $course = Course::create([
            'fullname' => $data['fullname'],
            'shortname' => $data['shortname'],
            'summary' => $data['summary'] ?? null,
            'numsections' => $data['numsections'] ?? 0,
            'startdate' => $data['startdate'] ?? null,
            'enddate' => $data['enddate'] ?? null,
            'teacher_id' => $data['teacher_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'image' => $data['image'] ?? null,
            'moodle_id' => $data['moodle_id'] ?? null,
            'visible' => $data['visible'] ?? true,
            'idnumber' => $data['idnumber'] ?? null,
            'format' => $data['format'] ?? 'topics',
            'hiddensections' => $data['hiddensections'] ?? 0,
            'coursedisplay' => $data['coursedisplay'] ?? 0,
            'lang' => $data['lang'] ?? null,
            'newsitems' => $data['newsitems'] ?? 5,
            'showgrades' => $data['showgrades'] ?? true,
            'showreports' => $data['showreports'] ?? false,
            'showactivitydates' => $data['showactivitydates'] ?? true,
            'maxbytes' => $data['maxbytes'] ?? 0,
            'enablecompletion' => $data['enablecompletion'] ?? true,
            'showcompletionconditions' => $data['showcompletionconditions'] ?? true,
            'groupmode' => $data['groupmode'] ?? 0,
            'groupmodeforce' => $data['groupmodeforce'] ?? false,
            'defaultgroupingid' => $data['defaultgroupingid'] ?? 0,
            'tags' => $data['tags'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de création (utiliser insertOrIgnore pour éviter doublons)
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'CREATE',
            'entity_type' => 'courses',
            'entity_id' => $course->id,
            'payload' => json_encode([
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'summary' => $course->summary,
                'numsections' => $course->numsections,
                'category_id' => $course->category_id,
                'teacher_id' => $course->teacher_id,
                'visible' => $course->visible,
                'idnumber' => $course->idnumber,
                'format' => $course->format,
                'hiddensections' => $course->hiddensections,
                'coursedisplay' => $course->coursedisplay,
                'lang' => $course->lang,
                'newsitems' => $course->newsitems,
                'showgrades' => $course->showgrades,
                'showreports' => $course->showreports,
                'showactivitydates' => $course->showactivitydates,
                'maxbytes' => $course->maxbytes,
                'enablecompletion' => $course->enablecompletion,
                'showcompletionconditions' => $course->showcompletionconditions,
                'groupmode' => $course->groupmode,
                'groupmodeforce' => $course->groupmodeforce,
                'defaultgroupingid' => $course->defaultgroupingid,
                'tags' => $course->tags,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $course;
    }

    /**
     * Mets à jour un cours localement et enqueue la sync.
     */
    public function update(Course $course, array $data): Course
    {
        $oldValues = $course->only([
            'fullname', 'shortname', 'summary', 'numsections', 
            'startdate', 'enddate', 'teacher_id', 'category_id',
            'visible', 'idnumber', 'format', 'hiddensections', 'coursedisplay',
            'lang', 'newsitems', 'showgrades', 'showreports', 'showactivitydates',
            'maxbytes', 'enablecompletion', 'showcompletionconditions',
            'groupmode', 'groupmodeforce', 'defaultgroupingid', 'tags'
        ]);

        $course->update([
            'fullname' => $data['fullname'] ?? $course->fullname,
            'shortname' => $data['shortname'] ?? $course->shortname,
            'summary' => $data['summary'] ?? $course->summary,
            'numsections' => $data['numsections'] ?? $course->numsections,
            'startdate' => $data['startdate'] ?? $course->startdate,
            'enddate' => $data['enddate'] ?? $course->enddate,
            'teacher_id' => $data['teacher_id'] ?? $course->teacher_id,
            'category_id' => $data['category_id'] ?? $course->category_id,
            'image' => $data['image'] ?? $course->image,
            'visible' => $data['visible'] ?? $course->visible,
            'idnumber' => $data['idnumber'] ?? $course->idnumber,
            'format' => $data['format'] ?? $course->format,
            'hiddensections' => $data['hiddensections'] ?? $course->hiddensections,
            'coursedisplay' => $data['coursedisplay'] ?? $course->coursedisplay,
            'lang' => $data['lang'] ?? $course->lang,
            'newsitems' => $data['newsitems'] ?? $course->newsitems,
            'showgrades' => $data['showgrades'] ?? $course->showgrades,
            'showreports' => $data['showreports'] ?? $course->showreports,
            'showactivitydates' => $data['showactivitydates'] ?? $course->showactivitydates,
            'maxbytes' => $data['maxbytes'] ?? $course->maxbytes,
            'enablecompletion' => $data['enablecompletion'] ?? $course->enablecompletion,
            'showcompletionconditions' => $data['showcompletionconditions'] ?? $course->showcompletionconditions,
            'groupmode' => $data['groupmode'] ?? $course->groupmode,
            'groupmodeforce' => $data['groupmodeforce'] ?? $course->groupmodeforce,
            'defaultgroupingid' => $data['defaultgroupingid'] ?? $course->defaultgroupingid,
            'tags' => $data['tags'] ?? $course->tags,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'courses',
                'entity_id' => $course->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'summary' => $course->summary,
                    'numsections' => $course->numsections,
                    'startdate' => $course->startdate,
                    'enddate' => $course->enddate,
                    'teacher_id' => $course->teacher_id,
                    'category_id' => $course->category_id,
                    'visible' => $course->visible,
                    'idnumber' => $course->idnumber,
                    'format' => $course->format,
                    'hiddensections' => $course->hiddensections,
                    'coursedisplay' => $course->coursedisplay,
                    'lang' => $course->lang,
                    'newsitems' => $course->newsitems,
                    'showgrades' => $course->showgrades,
                    'showreports' => $course->showreports,
                    'showactivitydates' => $course->showactivitydates,
                    'maxbytes' => $course->maxbytes,
                    'enablecompletion' => $course->enablecompletion,
                    'showcompletionconditions' => $course->showcompletionconditions,
                    'groupmode' => $course->groupmode,
                    'groupmodeforce' => $course->groupmodeforce,
                    'defaultgroupingid' => $course->defaultgroupingid,
                    'tags' => $course->tags,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $course;
    }

    /**
     * Supprime un cours localement et enqueue la sync.
     */
    public function delete(Course $course): void
    {
        // Marquer pour suppression plutôt que de supprimer immédiatement
        $course->update([
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de suppression
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'DELETE',
            'entity_type' => 'courses',
            'entity_id' => $course->id,
            'payload' => json_encode([
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'moodle_id' => $course->moodle_id,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    /**
     * Récupère tous les cours avec statut de sync.
     */
    public function getAll()
    {
        return Course::orderBy('fullname')->get();
    }

    /**
     * Récupère tous les cours d'une catégorie.
     */
    public function getByCategoryId(int $categoryId)
    {
        return Course::where('category_id', $categoryId)
            ->orderBy('fullname')
            ->get();
    }

    /**
     * Récupère un cours par ID.
     */
    public function getById(int $id): ?Course
    {
        return Course::find($id);
    }

    /**
     * Récupère les cours en attente de sync.
     */
    public function getPending()
    {
        return Course::pending()->get();
    }

    /**
     * Récupère les cours avec conflits.
     */
    public function getConflicts()
    {
        return Course::where('sync_status', 'conflict')->get();
    }
}
