<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Module;

class MoodleAssignmentService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;
    protected string $logFilePath;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];

        $this->logFilePath = storage_path('logs/moodle_actions.txt');
    }

    /**
     * Log an action to the sync file
     */
    public function logAction(string $action, array $data): void
    {
        $logEntry = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'data' => $data
        ];

        $logLine = json_encode($logEntry) . PHP_EOL;
        file_put_contents($this->logFilePath, $logLine, FILE_APPEND);

        Log::info("Moodle assignment action logged: {$action}");
    }

    /**
     * Récupérer tous les assignments d'un cours
     */

public function getAssignmentsByCourseId(int $courseId): array
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'mod_assign_get_assignments',
            'courseids[0]' => $courseId
        ]);

        $response = Http::get($this->apiUrl, $params);
        $data = $response->json();
        // Vérification approfondie pour récupérer les assignments
        if (isset($data['courses']) && is_array($data['courses']) && !empty($data['courses'])) {
            $assignments = $data['courses'][0]['assignments'] ?? [];

            // Vérification supplémentaire pour s'assurer que les assignments sont bien un tableau
            if (is_array($assignments)) {
                return $assignments;
            }

            Log::warning('Les assignments ne sont pas un tableau valide.', ['course_id' => $courseId]);
            return [];
        }

        Log::warning('Aucun cours trouvé ou réponse API invalide.', ['course_id' => $courseId]);
        return [];
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (getAssignmentsByCourseId): ' . $e->getMessage(), ['course_id' => $courseId]);
        return [];
    }
}

    /**
     * Récupérer les détails d'un assignment spécifique
     */
    public function getAssignmentById(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_assignments',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['courses']) && !empty($data['courses']) && 
                isset($data['courses'][0]['assignments']) && !empty($data['courses'][0]['assignments'])) {
                return $data['courses'][0]['assignments'][0];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getAssignmentById): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Synchroniser les assignments avec les modules existants
     */
    public function syncAssignmentsWithModules(int $courseId): void
    {
        try {
            $assignments = $this->getAssignmentsByCourseId($courseId);

            foreach ($assignments as $assignment) {
                // Trouver le module correspondant par le cmid (Course Module ID)
                $module = Module::where('moodle_id', $assignment['cmid'])->first();

                if ($module) {
                    // Nettoyer et préparer les données
                    $intro = $this->cleanHtmlContent($assignment['intro'] ?? '');
                    $activity = $this->cleanHtmlContent($assignment['activity'] ?? '');

                    // Mettre à jour le module avec les informations de l'assignment
                    $module->update([
                        'modplural' => $intro, // Stocker intro dans modplural
                        'file_path' => $activity,    // Stocker activity dans file_path
                    ]);

                    Log::info("Module mis à jour avec les données d'assignment", [
                        'module_id' => $module->id,
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid']
                    ]);

                    $this->logAction('assignment_sync', [
                        'module_id' => $module->id,
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid'],
                        'name' => $assignment['name']
                    ]);
                } else {
                    Log::warning("Aucun module trouvé pour l'assignment", [
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid'],
                        'name' => $assignment['name']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchronisation des assignments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Nettoyer le contenu HTML pour le stockage
     */
    private function cleanHtmlContent(string $content): string
    {
        // Supprimer les balises HTML mais garder le contenu textuel
        $cleanContent = strip_tags($content);
        
        // Remplacer les entités HTML
        $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Nettoyer les espaces en trop
        $cleanContent = trim(preg_replace('/\s+/', ' ', $cleanContent));
        
        return $cleanContent;
    }

    /**
     * Créer un nouvel assignment
     */
    public function createAssignment(int $courseId, array $assignmentData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_save_assignment',
                'assignmentdata' => $assignmentData
            ]);

            $response = Http::post($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (createAssignment): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les soumissions d'un assignment
     */
    public function getAssignmentSubmissions(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_submissions',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getAssignmentSubmissions): ' . $e->getMessage());
            return [];
        }
    }



public function getAssignmentDetails(int $moduleId, int $courseId): ?array
{
    try {
        // 1. Récupérer tous les assignments du cours
        $assignments = $this->getAssignmentsByCourseId($courseId);
        Log::info("Assignments trouvés pour le cours ", $assignments);

        // 2. Trouver l'assignment correspondant au module_id (cmid)
        foreach ($assignments as $assignment) {
            if (isset($assignment['cmid']) && $assignment['cmid'] == $moduleId) {
                Log::info("Assignment trouvé:", $assignment);
                
                // 3. Récupérer le fichier PDF (premier fichier d'intro si existe)
                $pdfData = null;
                if (!empty($assignment['introattachments'])) {
                    $firstFile = $assignment['introattachments'][0];
                    if ($firstFile['mimetype'] === 'application/pdf') {
                        $pdfData = [
                            'filename' => $firstFile['filename'],
                            'fileurl' => str_replace(
                                '?forcedownload=1', 
                                '?token='.$this->token, 
                                $firstFile['fileurl']
                            ),
                            'filesize' => $this->formatFileSize($firstFile['filesize'])
                        ];
                    }
                }

                // 4. Formater les données à retourner
                $result = [
                    'id' => $assignment['id'] ?? null,
                    'name' => $assignment['name'] ?? '',
                    'intro' => $this->cleanHtmlContent($assignment['intro'] ?? ''),
                    'activity' => $this->cleanHtmlContent($assignment['activity'] ?? ''),
                    'duedate' => $assignment['duedate'] ?? null,
                    'allowsubmissionsfromdate' => $assignment['allowsubmissionsfromdate'] ?? null,
                    'gradingduedate' => $assignment['gradingduedate'] ?? null,
                    'maxattempts' => $assignment['maxattempts'] ?? 1,
                    'grade' => $assignment['grade'] ?? 100,
                    'pdf_file' => $pdfData
                ];

                //Log::info('Données de l\'assignment à retourner:', $result);
                return $result;
            }
        }

        return null;
    } catch (\Exception $e) {
        Log::error('Erreur dans getAssignmentDetails: '.$e->getMessage(), [
            'module_id' => $moduleId,
            'course_id' => $courseId
        ]);
        return null;
    }
}

    // Fonctions utilitaires ajoutées
    private function formatFileSize(int $bytes): string 
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }
        return round($bytes / 1024, 2).' KB';
    }

    private function extractImportantConfigs(array $configs): array
    {
        $important = [
            'maxattempts' => 1,
            'teamsubmission' => 0,
            'requireallteammemberssubmit' => 0
        ];

        foreach ($configs as $config) {
            if (array_key_exists($config['name'], $important)) {
                $important[$config['name']] = $config['value'];
            }
        }

        return $important;
    }
}