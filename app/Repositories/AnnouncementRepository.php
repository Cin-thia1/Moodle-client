<?php

namespace App\Repositories;

use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

/**
 * AnnouncementRepository
 * Gère les opérations CRUD sur les annonces avec sync automatique.
 */
class AnnouncementRepository
{
    /**
     * Crée une annonce et l'enqueue en sync_queue.
     */
    public function create(int $courseId, array $data): Announcement
    {
        // Créer localement avec status pending
        $announcement = Announcement::create(array_merge($data, [
            'course_id' => $courseId,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]));

        // Auto-enqueuer à sync_queue
        $this->enqueue($announcement, 'create');

        return $announcement;
    }

    /**
     * Met à jour une annonce et l'enqueue.
     */
    public function update(Announcement $announcement, array $data): Announcement
    {
        $announcement->update($data);
        $announcement->update([
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Auto-enqueuer à sync_queue
        $this->enqueue($announcement, 'update');

        return $announcement;
    }

    /**
     * Supprime (soft delete) une annonce et l'enqueue.
     */
    public function delete(Announcement $announcement): void
    {
        $announcement->update([
            'status' => 0,
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Auto-enqueuer à sync_queue
        $this->enqueue($announcement, 'delete');
    }

    /**
     * Récupère toutes les annonces d'un cours.
     */
    public function getByCourseId(int $courseId): \Illuminate\Database\Eloquent\Collection
    {
        return Announcement::where('course_id', $courseId)->get();
    }

    /**
     * Récupère une annonce par ID.
     */
    public function getById(int $id): ?Announcement
    {
        return Announcement::find($id);
    }

    /**
     * Récupère les annonces en attente de sync.
     */
    public function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return Announcement::pending()->get();
    }

    /**
     * Récupère les annonces en conflit.
     */
    public function getConflicts(): \Illuminate\Database\Eloquent\Collection
    {
        return Announcement::conflicts()->get();
    }

    /**
     * Enqueue une opération en sync_queue.
     */
    private function enqueue(Announcement $announcement, string $operation): void
    {
        // Vérifier si une opération du même type existe déjà
        $existing = DB::table('sync_queue')
            ->where('entity_type', 'announcements')
            ->where('entity_id', $announcement->id)
            ->where('operation', strtoupper($operation))
            ->where('status', 'pending')
            ->first();

        if (!$existing) {
            DB::table('sync_queue')->insert([
                'entity_type' => 'announcements',
                'entity_id' => $announcement->id,
                'operation' => strtoupper($operation),
                'status' => 'pending',
                'payload' => json_encode([
                    'id' => $announcement->id,
                    'course_id' => $announcement->course_id,
                    'subject' => $announcement->subject,
                    'message' => $announcement->message,
                    'status' => $announcement->status,
                ]),
                'created_at' => now(),
            ]);
        }
    }
}
