<?php

namespace App\Repositories;

use App\Models\Section;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des sections avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 */
class SectionRepository
{
    /**
     * Crée une nouvelle section localement et enqueue la sync.
     */
    public function create(array $data): Section
    {
        $section = Section::create([
            'name' => $data['name'],
            'course_id' => $data['course_id'],
            'summary' => $data['summary'] ?? null,
            'position' => $data['position'] ?? 0,
            'visible' => $data['visible'] ?? 1,
            'moodle_id' => $data['moodle_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de création
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'CREATE',
            'entity_type' => 'sections',
            'entity_id' => $section->id,
            'payload' => json_encode([
                'name' => $section->name,
                'summary' => $section->summary,
                'position' => $section->position,
                'course_id' => $section->course_id,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $section;
    }

    /**
     * Mets à jour une section localement et enqueue la sync.
     */
    public function update(Section $section, array $data): Section
    {
        $oldValues = $section->only(['name', 'summary', 'position', 'visible']);

        $section->update([
            'name' => $data['name'] ?? $section->name,
            'summary' => $data['summary'] ?? $section->summary,
            'position' => $data['position'] ?? $section->position,
            'visible' => $data['visible'] ?? $section->visible,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'sections',
                'entity_id' => $section->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'name' => $section->name,
                    'summary' => $section->summary,
                    'position' => $section->position,
                    'visible' => $section->visible,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $section;
    }

    /**
     * Supprime une section localement et enqueue la sync.
     */
    public function delete(Section $section): void
    {
        // Marquer pour suppression plutôt que de supprimer immédiatement
        $section->update([
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de suppression
        DB::table('sync_queue')->insertOrIgnore([
            'operation' => 'DELETE',
            'entity_type' => 'sections',
            'entity_id' => $section->id,
            'payload' => json_encode([
                'name' => $section->name,
                'course_id' => $section->course_id,
                'moodle_id' => $section->moodle_id,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    /**
     * Récupère toutes les sections d'un cours.
     */
    public function getByCourseId(int $courseId)
    {
        return Section::where('course_id', $courseId)
            ->orderBy('position')
            ->get();
    }

    /**
     * Récupère une section par ID.
     */
    public function getById(int $id): ?Section
    {
        return Section::find($id);
    }

    /**
     * Récupère les sections en attente de sync.
     */
    public function getPending()
    {
        return Section::pending()->get();
    }

    /**
     * Récupère les sections avec conflits.
     */
    public function getConflicts()
    {
        return Section::where('sync_status', 'conflict')->get();
    }

    /**
     * Réorganise les positions des sections (drag & drop).
     */
    public function reorder(array $positions): void
    {
        foreach ($positions as $position => $sectionId) {
            $section = Section::find($sectionId);
            if ($section) {
                $section->update([
                    'position' => $position,
                    'sync_status' => 'pending',
                    'sync_action' => 'update',
                    'dirty' => 1,
                ]);

                // Enqueue pour sync
                DB::table('sync_queue')->updateOrInsert(
                    [
                        'operation' => 'UPDATE',
                        'entity_type' => 'sections',
                        'entity_id' => $sectionId,
                        'status' => 'pending',
                    ],
                    [
                        'payload' => json_encode(['position' => $position]),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
