<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Course;
use Exception;

class MoodleDocumentService
{
    protected MoodleWebService $webService;

    public function __construct(MoodleWebService $webService)
    {
        $this->webService = $webService;
    }

    /**
     * Synchronise les documents d'un cours depuis Moodle
     */
    public function syncCourseDocuments(int $courseId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            // Récupère les ressources (resources et dossiers)
            $resources = $this->webService->call('core_course_get_contents', [
                'courseid' => $course->moodle_id,
            ]);

            $synced = 0;
            $errors = 0;

            if (is_array($resources)) {
                foreach ($resources as $section) {
                    if (isset($section['modules']) && is_array($section['modules'])) {
                        foreach ($section['modules'] as $module) {
                            // Filtrer les ressources et dossiers
                            if (in_array($module['modname'] ?? null, ['resource', 'folder'])) {
                                try {
                                    $document = Document::updateOrCreate(
                                        ['moodle_id' => $module['id']],
                                        [
                                            'course_id' => $courseId,
                                            'filename' => $module['name'] ?? 'Document',
                                            'filepath' => $module['modname'] ?? '/',
                                            'mimetype' => $module['mimetype'] ?? 'application/octet-stream',
                                            'filesize' => $module['contents'][0]['filesize'] ?? 0,
                                            'file_url' => $module['contents'][0]['fileurl'] ?? null,
                                            'status' => 1,
                                            'file_date' => now(),
                                        ]
                                    );
                                    $synced++;
                                } catch (Exception $e) {
                                    logger()->error("Document sync error: " . $e->getMessage());
                                    $errors++;
                                }
                            }
                        }
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            logger()->error("Document service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les documents d'un cours
     */
    public function getCourseDocuments(int $courseId)
    {
        return Document::forCourse($courseId)
            ->visible()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crée un document
     */
    public function createDocument(int $courseId, int $userId, string $filename, array $data): Document
    {
        $data['course_id'] = $courseId;
        $data['user_id'] = $userId;
        $data['filename'] = $filename;
        $data['status'] = $data['status'] ?? 1;

        return Document::create($data);
    }

    /**
     * Met à jour un document
     */
    public function updateDocument(int $documentId, array $data): Document
    {
        $document = Document::findOrFail($documentId);
        $document->update($data);
        return $document;
    }

    /**
     * Supprime un document
     */
    public function deleteDocument(int $documentId): bool
    {
        return Document::destroy($documentId) > 0;
    }
}
