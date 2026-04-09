<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Repository pour gestion locale des documents avec sync.
 * Gère téléchargement local et synchronisation avec Moodle.
 */
class DocumentRepository
{
    /**
     * Ajoute un document localement et enqueue la sync.
     */
    public function store(int $courseId, array $data): Document
    {
        $document = Document::create([
            'course_id' => $courseId,
            'user_id' => $data['user_id'] ?? null,
            'filename' => $data['filename'],
            'filepath' => $data['filepath'] ?? '/',
            'mimetype' => $data['mimetype'] ?? null,
            'filesize' => $data['filesize'] ?? 0,
            'file_url' => $data['file_url'] ?? null,
            'status' => $data['status'] ?? 1,
            'file_date' => $data['file_date'] ?? now(),
            'moodle_id' => $data['moodle_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération
        DB::table('sync_queue')->insert([
            'operation' => 'CREATE',
            'entity_type' => 'documents',
            'entity_id' => $document->id,
            'payload' => json_encode([
                'course_id' => $courseId,
                'filename' => $document->filename,
                'filepath' => $document->filepath,
                'filesize' => $document->filesize,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $document;
    }

    /**
     * Met à jour les métadonnées d'un document et enqueue la sync.
     */
    public function update(Document $document, array $data): Document
    {
        $oldValues = $document->only(['filename', 'status', 'filepath']);

        $document->update([
            'filename' => $data['filename'] ?? $document->filename,
            'filepath' => $data['filepath'] ?? $document->filepath,
            'status' => $data['status'] ?? $document->status,
            'file_date' => $data['file_date'] ?? $document->file_date,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération
        DB::table('sync_queue')->insert([
            'operation' => 'UPDATE',
            'entity_type' => 'documents',
            'entity_id' => $document->id,
            'payload' => json_encode([
                'filename' => $document->filename,
                'filepath' => $document->filepath,
                'status' => $document->status,
                'old_values' => $oldValues,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $document;
    }

    /**
     * Télécharge et stocke localement un fichier depuis une URL Moodle.
     */
    public function downloadFromMoodle(Document $document, string $moodleFileUrl, string $token): bool
    {
        try {
            // Créer le chemin local
            $localPath = "courses/{$document->course_id}/documents/{$document->filename}";
            
            // Télécharger le fichier via cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $moodleFileUrl . '?token=' . $token);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $fileContent = curl_exec($ch);
            curl_close($ch);

            if ($fileContent === false) {
                return false;
            }

            // Stocker dans storage local
            Storage::disk('local')->put($localPath, $fileContent);

            // Mettre à jour le document avec le chemin local
            $document->update([
                'file_url' => Storage::disk('local')->path($localPath),
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Récupère un document par ID.
     */
    public function getById(int $id): ?Document
    {
        return Document::find($id);
    }

    /**
     * Récupère les documents d'un cours.
     */
    public function getByCourseId(int $courseId)
    {
        return Document::where('course_id', $courseId)
            ->with('creator')
            ->orderBy('filename')
            ->get();
    }

    /**
     * Récupère les documents visibles d'un cours.
     */
    public function getVisibleByCourseId(int $courseId)
    {
        return Document::where('course_id', $courseId)
            ->visible()
            ->with('creator')
            ->orderBy('filename')
            ->get();
    }

    /**
     * Récupère les documents en attente de sync.
     */
    public function getPending()
    {
        return Document::pending()->get();
    }

    /**
     * Récupère les documents avec conflits.
     */
    public function getConflicts()
    {
        return Document::where('sync_status', 'conflict')->get();
    }

    /**
     * Supprime un document et enqueue la suppression.
     */
    public function delete(Document $document): void
    {
        $document->update([
            'status' => 0,
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue la suppression
        DB::table('sync_queue')->insert([
            'operation' => 'DELETE',
            'entity_type' => 'documents',
            'entity_id' => $document->id,
            'payload' => json_encode([
                'filename' => $document->filename,
                'filepath' => $document->filepath,
                'moodle_id' => $document->moodle_id,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }
}
