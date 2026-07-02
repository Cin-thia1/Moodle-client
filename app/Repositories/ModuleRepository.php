<?php

namespace App\Repositories;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des modules avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 * 
 * Supporte tous les types de modules: assign, quiz, resource, page, url, forum, label, etc.
 */
class ModuleRepository
{
    /**
     * Crée un nouveau module localement et enqueue la sync.
     */
    public function create(array $data): Module
    {
        $module = Module::create([
            'name' => $data['name'],
            'section_id' => $data['section_id'],
            'modname' => $data['modname'],
            'modplural' => $data['modplural'] ?? '',
            'intro' => $data['intro'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'downloadcontent' => $data['downloadcontent'] ?? false,
            'position' => $data['position'] ?? 0,
            'visible' => $data['visible'] ?? 1,
            'completion' => $data['completion'] ?? 0,
            // Champs spécifiques aux assignments
            'duedate' => $data['duedate'] ?? null,
            'allowsubmissionsfromdate' => $data['allowsubmissionsfromdate'] ?? null,
            'cutoffdate' => $data['cutoffdate'] ?? null,
            'maxattempts' => $data['maxattempts'] ?? 1,
            'grade' => $data['grade'] ?? 100,
            // Champs spécifiques aux quiz
            'timeopen' => $data['timeopen'] ?? null,
            'timeclose' => $data['timeclose'] ?? null,
            'timelimit' => $data['timelimit'] ?? null,
            'attempts' => $data['attempts'] ?? 0,
            'grademethod' => $data['grademethod'] ?? 0,
            'shuffleanswers' => $data['shuffleanswers'] ?? false,
            'questionsperpage' => $data['questionsperpage'] ?? 1,
            // Synchronisation
            'moodle_id' => $data['moodle_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de création
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'CREATE',
                'entity_type' => 'modules',
                'entity_id' => $module->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'name' => $module->name,
                    'modname' => $module->modname,
                    'intro' => $module->intro,
                    'section_id' => $module->section_id,
                    'visible' => $module->visible,
                ]),
                'created_at' => now(),
            ]
        );

        return $module;
    }

    /**
     * Mets à jour un module localement et enqueue la sync.
     */
    public function update(Module $module, array $data): Module
    {
        $oldValues = $module->only([
            'name', 'intro', 'position', 'visible', 'completion',
            'duedate', 'allowsubmissionsfromdate', 'cutoffdate',
            'timeopen', 'timeclose', 'timelimit', 'attempts'
        ]);

        $module->update([
            'name' => $data['name'] ?? $module->name,
            'intro' => $data['intro'] ?? $module->intro,
            'position' => $data['position'] ?? $module->position,
            'visible' => $data['visible'] ?? $module->visible,
            'completion' => $data['completion'] ?? $module->completion,
            'duedate' => $data['duedate'] ?? $module->duedate,
            'allowsubmissionsfromdate' => $data['allowsubmissionsfromdate'] ?? $module->allowsubmissionsfromdate,
            'cutoffdate' => $data['cutoffdate'] ?? $module->cutoffdate,
            'timeopen' => $data['timeopen'] ?? $module->timeopen,
            'timeclose' => $data['timeclose'] ?? $module->timeclose,
            'timelimit' => $data['timelimit'] ?? $module->timelimit,
            'attempts' => $data['attempts'] ?? $module->attempts,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'UPDATE',
                'entity_type' => 'modules',
                'entity_id' => $module->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'name' => $module->name,
                    'intro' => $module->intro,
                    'position' => $module->position,
                    'visible' => $module->visible,
                    'completion' => $module->completion,
                    'old_values' => $oldValues,
                ]),
                'created_at' => now(),
            ]
        );

        return $module;
    }

    /**
     * Supprime un module localement et enqueue la sync.
     */
    public function delete(Module $module): void
    {
        // Marquer pour suppression plutôt que de supprimer immédiatement
        $module->update([
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de suppression
        DB::table('sync_queue')->updateOrInsert(
            [
                'operation' => 'DELETE',
                'entity_type' => 'modules',
                'entity_id' => $module->id,
                'status' => 'pending',
            ],
            [
                'payload' => json_encode([
                    'name' => $module->name,
                    'modname' => $module->modname,
                    'moodle_id' => $module->moodle_id,
                ]),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Récupère tous les modules d'une section.
     */
    public function getBySectionId(int $sectionId)
    {
        return Module::where('section_id', $sectionId)
            ->orderBy('position')
            ->get();
    }

    /**
     * Récupère un module par ID.
     */
    public function getById(int $id): ?Module
    {
        return Module::find($id);
    }

    /**
     * Récupère les modules en attente de sync.
     */
    public function getPending()
    {
        return Module::pending()->get();
    }

    /**
     * Récupère les modules avec conflits.
     */
    public function getConflicts()
    {
        return Module::where('sync_status', 'conflict')->get();
    }

    /**
     * Réorganise les positions des modules.
     */
    public function reorder(array $positions): void
    {
        foreach ($positions as $position => $moduleId) {
            $module = Module::find($moduleId);
            if ($module) {
                $module->update([
                    'position' => $position,
                    'sync_status' => 'pending',
                    'sync_action' => 'update',
                    'dirty' => 1,
                ]);

                // Enqueue pour sync
                DB::table('sync_queue')->updateOrInsert(
                    [
                        'operation' => 'UPDATE',
                        'entity_type' => 'modules',
                        'entity_id' => $moduleId,
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
