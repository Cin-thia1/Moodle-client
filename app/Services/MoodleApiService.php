<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Service centralisé pour les appels à l'API Web Services de Moodle.
 * Enveloppe MoodleWebService avec gestion d'erreurs enrichie + detection de connexion.
 */
class MoodleApiService
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout = 30; // secondes
    protected array $categoryWriteFunctions = [
        'core_course_create_categories',
        'core_course_update_categories',
        'core_course_delete_categories',
    ];

    public function __construct()
    {
        $this->baseUrl = rtrim(config('moodle.api_url'), '/');
        $this->token = config('moodle.api_token');
    }

    /**
     * Effectue un appel GET à l'API Web Services de Moodle.
     * Utilisé pour les lectures (get_*, etc.)
     * 
     * @param string $function Nom de la fonction Moodle
     * @param array $params Paramètres à passer
     * @return mixed Réponse JSON parsée
     * @throws Exception
     */
    public function callGet(string $function, array $params = []): mixed
    {
        return $this->call($function, $params, 'GET');
    }

    /**
     * Effectue un appel POST à l'API Web Services de Moodle.
     * Utilisé pour les écritures (create_*, update_*, delete_*, etc.)
     * 
     * @param string $function Nom de la fonction Moodle
     * @param array $params Paramètres à passer
     * @return mixed Réponse JSON parsée
     * @throws Exception
     */
    public function callPost(string $function, array $params = []): mixed
    {
        return $this->call($function, $params, 'POST');
    }

    /**
     * Effectue un appel à l'API Web Services de Moodle.
     * 
     * @param string $function Nom de la fonction Moodle (ex: core_course_get_courses)
     * @param array $params Paramètres à passer
     * @param string $method GET ou POST (par défaut GET pour compatibilité)
     * @return mixed Réponse JSON parsée
     * 
     * @throws Exception Si erreur HTTP, timeout, ou exception Moodle
     */
    public function call(string $function, array $params = [], string $method = 'GET'): mixed
    {
        try {
            $token = $this->resolveToken($function);
            $baseParams = [
                'wstoken' => $token,
                'wsfunction' => $function,
                'moodlewsrestformat' => 'json',
                ...$params,
            ];

            // baseUrl contient déjà /webservice/rest/server.php
            if (strtoupper($method) === 'POST') {
                $response = Http::timeout($this->timeout)->asForm()->post($this->baseUrl, $baseParams);
            } else {
                $response = Http::timeout($this->timeout)->get($this->baseUrl, $baseParams);
            }

            // Vérifier les erreurs HTTP
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error("[Moodle API] HTTP Error {$response->status()}: {$response->body()}");
                throw new Exception(
                    "HTTP Error {$response->status()}: {$response->body()}"
                );
            }

            $data = $response->json();

            if ($data === null) {
                \Illuminate\Support\Facades\Log::debug('[Moodle API] response body not json', [
                    'function' => $function,
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            // Vérifier les erreurs Moodle (exception field dans la réponse)
            if (is_array($data) && isset($data['exception'])) {
                $message = $data['message'] ?? '';
                $errorcode = $data['errorcode'] ?? '';
                
                \Illuminate\Support\Facades\Log::error("[Moodle API] Exception: {$data['exception']}", [
                    'function' => $function,
                    'method' => $method,
                    'errorcode' => $errorcode,
                    'message' => $message,
                    'params_keys' => array_keys($params),
                ]);
                
                throw new Exception(
                    "Moodle Exception: {$data['exception']} (code: {$errorcode}) - {$message}"
                );
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] Connection error: {$e->getMessage()}");
            throw new Exception("Connexion à Moodle impossible: {$e->getMessage()}");
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] Request error: {$e->getMessage()}");
            throw new Exception("Erreur de requête Moodle: {$e->getMessage()}");
        }
    }

    protected function resolveToken(string $function): string
    {
        if (in_array($function, $this->categoryWriteFunctions, true)) {
            $adminToken = config('moodle.api_admin_token');
            if (!empty($adminToken)) {
                return $adminToken;
            }
        }

        return $this->token;
    }

    /**
     * Test de connexion à Moodle.
     * Effectue un simple GET HTTP sur la racine du site Moodle.
     * 
     * @return bool true si le serveur répond, false sinon
     */
    public function ping(): bool
    {
        try {
            // Ping la racine du site Moodle (pas l'endpoint webservice)
            $serverRoot = preg_replace('#/webservice/rest/server\.php$#', '', $this->baseUrl);
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get($serverRoot);
            return !$response->serverError();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Détecte si on est en ligne (connecté à Moodle).
     * 
     * @return bool true si en ligne, false sinon
     */
    public function isOnline(): bool
    {
        return $this->ping();
    }

    /**
     * Récupère les informations du site (nom, version, etc.).
     * 
     * @return array Informations du site
     * @throws Exception
     */
    public function getSiteInfo(): array
    {
        return $this->call('core_webservice_get_site_info');
    }

    /**
     * Récupère les cours de l'utilisateur connecté.
     * 
     * @return array Liste des cours
     * @throws Exception
     */
    /*public function getUserCourses(): array
    {
        return $this->call('core_enrol_get_users_courses', [
            'userid' => 0, // 0 = utilisateur courant
        ]);
    }*/
        public function getUserCourses(): array
{
    try {
        $courses = $this->call('core_course_get_courses', []);
        return array_values(array_filter($courses, fn($c) => ($c['id'] ?? 0) !== 1));
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Erreur getUserCourses: {$e->getMessage()}");
        return [];
    }
}

    /**
     * Récupère tous les cours du système Moodle.
     * Utilise core_course_get_courses.
     * 
     * @return array Liste de tous les cours
     * @throws Exception
     */
    public function getAllCourses(): array
    {
        return $this->call('core_course_get_courses', []);
    }

    /**
     * Supprime un cours sur Moodle.
     * 
     * @param int $courseId ID du cours Moodle
     * @return void
     * @throws Exception
     */
    public function deleteCourse(int $courseId): void
    {
        $this->call('core_course_delete_courses', [
            'courseids[0]' => $courseId,
        ]);
    }

    /**
     * Récupère les catégories de cours.
     * 
     * @param int|null $categoryId ID de la catégorie parent (optionnel)
     * @return array Liste des catégories
     * @throws Exception
     */
    public function getCategories(?int $categoryId = null): array
    {
        $criteria = $categoryId ? ['criteria' => [['key' => 'parent', 'value' => $categoryId]]] : [];
        return $this->call('core_course_get_categories', $criteria);
    }

    /**
     * Supprime une catégorie sur Moodle.
     * 
     * @param int $categoryId ID de la catégorie Moodle
     * @return void
     * @throws Exception
     */
    public function deleteCategory(int $categoryId): void
    {
        $this->call('core_course_delete_categories', [
            'categories[0][id]' => $categoryId,
            'categories[0][recursive]' => 1,
        ]);
    }

    /**
     * Récupère le contenu d'un cours (sections et modules).
     * 
     * @param int $courseId ID du cours Moodle
     * @return array Contenu du cours
     * @throws Exception
     */
    public function getCourseContents(int $courseId): array
    {
        return $this->call('core_course_get_contents', [
            'courseid' => $courseId,
        ]);
    }

    /**
     * Récupère les utilisateurs inscrits dans un cours.
     * 
     * @param int $courseId ID du cours Moodle
     * @return array Liste des utilisateurs
     * @throws Exception
     */
    public function getEnrolledUsers(int $courseId): array
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $courseId,
        ]);
    }

    /**
     * Enrôle un utilisateur dans un cours.
     * Utilise enrol_manual_enrol_users (enrôlement manuel).
     * 
     * @param int $userId ID de l'utilisateur Moodle
     * @param int $courseId ID du cours Moodle
     * @param string $roleShortname Shortname du rôle (student, teacher, editingteacher, etc.)
     * @return void
     * @throws Exception
     */
    public function enrollUser(int $userId, int $courseId, string $roleShortname = 'student'): void
    {
        $this->call('enrol_manual_enrol_users', [
            'enrolments[0][userid]' => $userId,
            'enrolments[0][courseid]' => $courseId,
            'enrolments[0][roleid]' => $this->getRoleIdByShortname($roleShortname),
        ]);
    }

    /**
     * Désenrôle un utilisateur d'un cours.
     * Utilise core_enrol_unenrol_user_enrolment.
     * 
     * @param int $enrolmentId ID de l'enrôlement (participation) Moodle
     * @return void
     * @throws Exception
     */
    public function unenrollUser(int $enrolmentId): void
    {
        $this->call('core_enrol_unenrol_user_enrolment', [
            'enrolmentid' => $enrolmentId,
        ]);
    }

    /**
     * Obtient l'ID de rôle à partir du shortname.
     * Mapping standard Moodle.
     * 
     * @param string $shortname student, teacher, editingteacher, manager, etc.
     * @return int ID du rôle
     */
    protected function getRoleIdByShortname(string $shortname): int
    {
        // Mapping standard Moodle des roles
        $roles = [
            'student' => 5,
            'teacher' => 3,
            'editingteacher' => 4,
            'manager' => 1,
            'coursecreator' => 2,
            'guest' => 6,
            'user' => 9,
        ];

        return $roles[strtolower($shortname)] ?? 5; // Par défaut student
    }

    /**
     * Récupère les tentatives de quiz pour un utilisateur et un quiz.
     * 
     * @param int $quizId ID du quiz Moodle (module)
     * @param int $userId ID de l'utilisateur Moodle
     * @return array Liste des tentatives
     * @throws Exception
     */
    public function getQuizAttempts(int $quizId, int $userId): array
    {
        try {
            return $this->call('mod_quiz_get_user_attempts', [
                'quizid' => $quizId,
                'userid' => $userId,
            ]);
        } catch (\Exception $e) {
            // Si la fonction n'existe pas, retourner un array vide
            return [];
        }
    }

    /**
     * Récupère les grades d'un utilisateur pour un quiz.
     * 
     * @param int $quizId ID du quiz Moodle
     * @param int $userId ID de l'utilisateur Moodle
     * @return array Données de grade
     * @throws Exception
     */
    public function getGradeForUser(int $quizId, int $userId): array
    {
        try {
            return $this->call('mod_quiz_get_best_grade', [
                'quizid' => $quizId,
                'userid' => $userId,
            ]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Soumet une réponse à une question de quiz.
     * 
     * @param int $attemptId ID de la tentative Moodle
     * @param int $slotId Numéro de la question dans la tentative
     * @param array $answers Réponses soumises
     * @return void
     * @throws Exception
     */
    public function submitQuizAnswer(int $attemptId, int $slotId, array $answers): void
    {
        $this->call('mod_quiz_save_attempt', [
            'attemptid' => $attemptId,
            'data[' . $slotId . ']' => json_encode($answers),
        ]);
    }

    /**
     * Finalise une tentative de quiz.
     * 
     * @param int $attemptId ID de la tentative Moodle
     * @return void
     * @throws Exception
     */
    public function finishQuizAttempt(int $attemptId): void
    {
        $this->call('mod_quiz_finish_attempt', [
            'attemptid' => $attemptId,
        ]);
    }

    /**
     * Récupère les fichiers d'un cours.
     * Utilise core_files_get_files via l'API Web Services.
     * 
     * @param int $courseId ID du cours Moodle
     * @param string $component Component (ex: 'course')
     * @param string $fileArea Area (ex: 'content')
     * @return array Liste des fichiers
     * @throws Exception
     */
    public function getCourseFiles(int $courseId, string $component = 'course', string $fileArea = 'content'): array
    {
        try {
            return $this->call('core_files_get_files', [
                'contextid' => $courseId,
                'component' => $component,
                'filearea' => $fileArea,
                'itemid' => 0,
                'filepath' => '/',
            ]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Télécharge un fichier depuis Moodle.
     * Retourne l'URL de téléchargement du fichier.
     * 
     * @param int $fileid ID du fichier Moodle
     * @param string $filename Nom du fichier
     * @return string URL du fichier
     */
    public function getFileDownloadUrl(int $fileid, string $filename): string
    {
        return rtrim($this->baseUrl, '/') . "/pluginfile.php/{$fileid}/{$filename}?token={$this->token}";
    }

    /**
     * Télécharge physiquement un fichier depuis une URL Moodle nécessitant le token,
     * et le sauvegarde à l'emplacement indiqué.
     * 
     * @param string $fileUrl L'URL du fichier sur Moodle (ex: issue de contents)
     * @param string $destinationPath Le chemin de destination complet
     * @return bool true si succès, false sinon
     */
    public function downloadFile(string $fileUrl, string $destinationPath): bool
    {
        try {
            // Ajouter le token à l'URL si ce n'est pas déjà fait
            if (!str_contains($fileUrl, 'token=')) {
                $separator = str_contains($fileUrl, '?') ? '&' : '?';
                $fileUrl .= $separator . 'token=' . $this->token;
            }

            $response = \Illuminate\Support\Facades\Http::timeout(120)->withOptions([
                'stream' => true,
            ])->get($fileUrl);

            if ($response->successful()) {
                // S'assurer que le dossier de destination existe
                $dir = dirname($destinationPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                file_put_contents($destinationPath, $response->body());
                return true;
            }
            
            \Illuminate\Support\Facades\Log::error("[Moodle API] Echec du téléchargement du fichier {$fileUrl} : HTTP {$response->status()}");
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] Erreur lors du téléchargement de {$fileUrl} : {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Analyse un contenu HTML pour extraire les images base64 ou locales
     * afin de les uploader comme brouillons (drafts) sur Moodle.
     * 
     * @param string $html Contenu HTML
     * @return array Tableau d'images contenant 'src', 'is_base64', 'data', 'mime', 'extension'
     */
    public function extractImagesFromHtml(string $html): array
    {
        $images = [];
        preg_match_all('/<img[^>]+src=(["\'])(.*?)\1/i', $html, $matches);
        
        foreach ($matches[2] as $src) {
            // Ignorer les URLs externes ou Moodle
            if (str_starts_with($src, 'http') && !str_starts_with($src, config('app.url'))) {
                continue;
            }
            
            if (str_starts_with($src, 'data:image')) {
                // C'est du base64
                preg_match('/data:image\/(.*?);base64,(.*)/i', $src, $base64Match);
                if (count($base64Match) === 3) {
                    $images[] = [
                        'original_src' => $src,
                        'is_base64' => true,
                        'extension' => $base64Match[1],
                        'data' => base64_decode($base64Match[2])
                    ];
                }
            } else {
                // Fichier local (chemin relatif ou absolu local)
                $images[] = [
                    'original_src' => $src,
                    'is_base64' => false,
                    'path' => $src
                ];
            }
        }
        
        return $images;
    }


    /**
     * Télécharge un fichier vers Moodle.
     * Utilise l'API de téléchargement de fichiers.
     * 
     * @param string $filePath Chemin local du fichier
     * @param string $filename Nom du fichier
     * @param int $contextId ID du contexte (course, module, etc.)
     * @param string $component Component
     * @param string $fileArea Area
     * @return int ID du fichier créé
     * @throws Exception
     */
    public function uploadFile(string $filePath, string $filename, int $contextId, string $component = 'course', string $fileArea = 'content'): int
    {
        if (!file_exists($filePath)) {
            throw new Exception("Fichier local introuvable: {$filePath}");
        }

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new Exception("Impossible de lire le fichier local: {$filePath}");
        }

        $result = $this->callPost('core_files_upload', [
            'contextid' => $contextId,
            'component' => $component,
            'filearea' => $fileArea,
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
            'filecontent' => base64_encode($fileContent),
        ]);

        \Illuminate\Support\Facades\Log::debug('[Moodle API] core_files_upload result', [
            'contextid' => $contextId,
            'component' => $component,
            'filearea' => $fileArea,
            'filename' => $filename,
            'result' => $result,
        ]);

        if (!is_array($result) || !isset($result['id'])) {
            throw new Exception('Réponse Moodle invalide lors de l’upload de fichier');
        }

        return (int) $result['id'];
    }

    /**
     * Crée un draft utilisateur pour l'upload de fichier.
     *
     * @param string $filename Nom du fichier
     * @return int Draft itemid retourné par Moodle
     * @throws Exception
     */
    public function createUserFileDraft(string $filename): array
    {
        $result = $this->call('core_files_get_unused_draft_itemid', []);

        if (!is_array($result) || !isset($result['itemid']) || !isset($result['contextid'])) {
            throw new Exception('Impossible de créer le draft Moodle pour le fichier');
        }

        return [
            'itemid' => (int) $result['itemid'],
            'contextid' => (int) $result['contextid'],
        ];
    }

    /**
     * Téléverse un fichier vers l'aire de brouillon utilisateur sur Moodle.
     *
     * @param string $filePath Chemin local du fichier
     * @param string $filename Nom du fichier
     * @param int|null $draftId ID du brouillon existant (créé si null)
     * @param int|null $contextId ID du contexte Moodle (créé si null)
     * @return int Draft itemid retourné par Moodle
     * @throws Exception
     */
    public function uploadFileToDraft(string $filePath, string $filename, ?int $draftId = null, ?int $contextId = null): int
    {
        if (!$draftId || !$contextId) {
            $draftInfo = $this->createUserFileDraft($filename);
            $draftId = $draftInfo['itemid'];
            $contextId = $draftInfo['contextid'];
        }

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new Exception("Impossible de lire le fichier local: {$filePath}");
        }

        $result = $this->callPost('core_files_upload', [
            'contextid' => $contextId,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftId,
            'filepath' => '/',
            'filename' => $filename,
            'filecontent' => base64_encode($fileContent),
        ]);

        if (!is_array($result) || !isset($result['itemid'])) {
            throw new Exception('Réponse Moodle invalide lors de l’upload de fichier vers draft : ' . json_encode($result));
        }

        return $draftId;
    }

    /**
     * Récupère les informations d'un fichier Moodle.
     * 
     * @param int $fileid ID du fichier Moodle
     * @return array Informations du fichier
     * @throws Exception
     */
    public function getFileInfo(int $fileid): array
    {
        try {
            return $this->call('core_files_get_file_info', [
                'fileid' => $fileid,
            ]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupère les soumissions d'un devoir pour un utilisateur.
     * 
     * @param int $assignmentId ID du devoir Moodle (module)
     * @param int $userId ID de l'utilisateur Moodle
     * @return array Liste des soumissions
     * @throws Exception
     */
    public function getAssignmentSubmissions(int $assignmentId, int $userId): array
    {
        try {
            return $this->call('mod_assign_get_submissions', [
                'assignmentids[0]' => $assignmentId,
                'userids[0]' => $userId,
            ]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Soumet un devoir.
     * 
     * @param int $assignmentId ID du devoir Moodle
     * @param int $userId ID de l'utilisateur Moodle
     * @param array $submissionData Données de la soumission
     * @return void
     * @throws Exception
     */
    public function submitAssignment(int $assignmentId, int $userId, array $submissionData): void
    {
        $this->call('mod_assign_submit_for_grading', [
            'assignmentid' => $assignmentId,
            'userid' => $userId,
            'data' => json_encode($submissionData),
        ]);
    }

    /**
     * Enregistre une note pour une soumission.
     * 
     * @param int $assignmentId ID du devoir Moodle
     * @param int $userId ID de l'utilisateur Moodle
     * @param float $grade Note à enregistrer
     * @param string $feedback Commentaire (feedback)
     * @return void
     * @throws Exception
     */
    public function saveAssignmentGrade(int $assignmentId, int $userId, float $grade, string $feedback = ''): void
    {
        $this->call('mod_assign_save_grade', [
            'assignmentid' => $assignmentId,
            'userid' => $userId,
            'grade' => $grade,
            'feedback' => $feedback,
        ]);
    }

    /**
     * Récupère les annonces (discussions de forum) d'un cours.
     * Utilise mod_forum_get_forum_discussions.
     * 
     * @param int $courseId ID du cours Moodle
     * @param int $forumId ID du forum (optionnel, sinon tous les forums du cours)
     * @return array Liste des discussions/annonces
     * @throws Exception
     */
    public function getAnnouncements(int $courseId, ?int $forumId = null): array
    {
        if (!$forumId) {
            $forumId = $this->getAnnouncementForumId($courseId);
            if (!$forumId) {
                \Illuminate\Support\Facades\Log::warning("[Moodle API] Aucun forum d'annonces trouvé pour le cours {$courseId}.");
                return [];
            }
        }

        $result = $this->call('mod_forum_get_forum_discussions', [
            'forumid' => $forumId,
        ]);

        return is_array($result) ? $result : [];
    }

    /**
     * Crée une annonce (discussion de forum).
     * Utilise mod_forum_add_discussion.
     * 
     * @param int $forumId ID du forum Moodle
     * @param string $subject Sujet de l'annonce
     * @param string $message Contenu de l'annonce
     * @param int $userId ID de l'utilisateur auteur
     * @param int|null $draftId ID du brouillon pour les images inline
     * @return int ID de la nouvelle discussion créée
     * @throws Exception
     */
    public function createAnnouncement(int $forumId, string $subject, string $message, int $userId, ?int $draftId = null): int
    {
        $params = [
            'forumid' => $forumId,
            'subject' => $subject,
            'message' => $message,
            'userid' => $userId,
        ];

        if ($draftId) {
            $params['options[0][name]'] = 'draftitemid';
            $params['options[0][value]'] = $draftId;
        }

        $result = $this->call('mod_forum_add_discussion', $params);
        
        return $result['discussionid'] ?? intval($result);
    }

    /**
     * Met à jour une annonce (discussion de forum).
     * Utilise mod_forum_update_discussion.
     * 
     * @param int $discussionId ID de la discussion Moodle
     * @param string $subject Sujet mis à jour
     * @param string $message Contenu mis à jour
     * @return void
     * @throws Exception
     */
    public function updateAnnouncement(int $discussionId, string $subject, string $message): void
    {
        $this->call('mod_forum_update_discussion', [
            'discussionid' => $discussionId,
            'subject' => $subject,
            'message' => $message,
        ]);
    }

    /**
     * Supprime une annonce (discussion de forum).
     * Utilise mod_forum_delete_discussion.
     * 
     * @param int $discussionId ID de la discussion Moodle
     * @return void
     * @throws Exception
     */
    public function deleteAnnouncement(int $discussionId): void
    {
        $this->call('mod_forum_delete_discussion', [
            'discussionid' => $discussionId,
        ]);
    }

    /**
     * Récupère le forum principal d'un cours (announcement forum).
     * 
     * @param int $courseId ID du cours Moodle
     * @return ?int ID du forum annonces, ou null
     * @throws Exception
     */
    public function getAnnouncementForumId(int $courseId): ?int
    {
        try {
            $forums = $this->call('mod_forum_get_forums_by_courses', [
                'courseids' => [$courseId],
            ]);
            
            if (is_array($forums) && count($forums) > 0) {
                // Récupérer le premier forum (normalement le forum annonces)
                return $forums[0]['id'] ?? null;
            }
        } catch (Exception $e) {
            // Si la API n'existe pas, retourner null
            return null;
        }
        
        return null;
    }

    /**
     * Récupère les utilisateurs du site Moodle.
     * Utilise core_user_get_users pour lister les utilisateurs.
     * 
     * @param array $criteria Critères de recherche (ex: ['key' => 'email', 'value' => 'user@example.com'])
     * @return array Liste des utilisateurs
     * @throws Exception
     */
    public function getUsers(array $criteria = []): array
    {
        try {
            if (empty($criteria)) {
                \Illuminate\Support\Facades\Log::warning('[Moodle API] getUsers() appelé sans critères. Aucun utilisateur récupéré.');
                return [];
            }

            $params = [];
            foreach ($criteria as $index => $criterion) {
                $params["criteria[{$index}][key]"] = $criterion['key'] ?? '';
                $params["criteria[{$index}][value]"] = $criterion['value'] ?? '';
            }

            return $this->call('core_user_get_users', $params);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] getUsers error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Crée un nouvel utilisateur sur Moodle.
     * Utilise core_user_create_users.
     * 
     * @param string $username Nom d'utilisateur unique
     * @param string $email Email unique
     * @param string $firstname Prénom
     * @param string $lastname Nom
     * @param string $password Mot de passe (optionnel, Moodle génère un email)
     * @return int ID du nouvel utilisateur créé
     * @throws Exception
     */
    public function createUser(string $username, string $email, string $firstname, string $lastname, string $password = ''): int
    {
        try {
            // Valider les paramètres localement d'abord
            if (empty($username) || strlen($username) < 3) {
                throw new Exception("Username must be at least 3 characters");
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }
            if (empty($firstname)) {
                throw new Exception("Firstname is required");
            }
            if (empty($lastname)) {
                throw new Exception("Lastname is required");
            }

            $params = [
                'users[0][username]' => $username,
                'users[0][email]' => $email,
                'users[0][firstname]' => $firstname,
                'users[0][lastname]' => $lastname,
                'users[0][auth]' => 'manual',
            ];

            // Password optionnel - seulement si fourni
            if (!empty($password)) {
                $params['users[0][password]'] = $password;
            }

            $result = $this->callPost('core_user_create_users', $params);

            // La réponse est une liste d'objets avec 'id' et 'username'
            $userId = null;
            
            if (is_array($result) && count($result) > 0) {
                $userId = $result[0]['id'] ?? null;
            }

            if (!$userId) {
                throw new Exception("Impossible d'extraire l'ID utilisateur de la réponse Moodle: " . json_encode($result));
            }

            \Illuminate\Support\Facades\Log::info("[Moodle API] User créé: {$username} (ID: {$userId})");

            return $userId;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] Erreur création user {$username}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Met à jour un utilisateur sur Moodle.
     * Utilise core_user_update_users.
     * 
     * @param int $userId ID de l'utilisateur Moodle
     * @param array $data Données à mettre à jour (email, firstname, lastname, etc.)
     * @return void
     * @throws Exception
     */
    public function updateUser(int $userId, array $data): void
    {
        $params = ['users[0][id]' => $userId];
        foreach ($data as $key => $value) {
            $params["users[0][{$key}]"] = $value;
        }

        $this->callPost('core_user_update_users', $params);
    }

    /**
     * Supprime un utilisateur sur Moodle (ou le désactive).
     * Utilise core_user_delete_users.
     * 
     * @param int $userId ID de l'utilisateur Moodle
     * @return void
     * @throws Exception
     */
    public function deleteUser(int $userId): void
    {
        $this->call('core_user_delete_users', [
            'userids[0]' => $userId,
        ]);
    }

    /**
     * Récupère les détails d'un utilisateur par ID.
     * 
     * @param int $userId ID de l'utilisateur Moodle
     * @return array Données de l'utilisateur
     * @throws Exception
     */
    public function getUserById(int $userId): array
    {
        try {
            $result = $this->call('core_user_get_users', [
                'criteria[0][key]' => 'id',
                'criteria[0][value]' => $userId,
            ]);

            return $result['users'][0] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Tente d'authentifier un utilisateur auprès de Moodle en demandant un token.
     * Cette méthode est utilisée pour vérifier les identifiants Moodle sans avoir le hash.
     * 
     * @param string $username
     * @param string $password
     * @return string|null Le token si succès, null sinon
     */
    public function authenticateUser(string $username, string $password): ?string
    {
        try {
            // L'endpoint de token est à la racine du site Moodle, pas dans /webservice/rest/
            $serverRoot = preg_replace('#/webservice/rest/server\.php$#', '', $this->baseUrl);
            $url = "{$serverRoot}/login/token.php";
            $service = config('moodle.api_service', 'moodle_mobile_app');
            
            \Illuminate\Support\Facades\Log::info("[Moodle API] Auth attempt: url={$url}, user={$username}, service={$service}");
            
            $response = \Illuminate\Support\Facades\Http::timeout($this->timeout)->get($url, [
                'username' => $username,
                'password' => $password,
                'service'  => $service,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['token'])) {
                    return $data['token'];
                }
            }

            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("[Moodle API] Auth error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Vérifie silencieusement la validité d'un token Moodle.
     * Réutilise getSiteInfoByToken : si le token est invalide, Moodle retourne une erreur.
     * En cas de doute (exception réseau), on considère le token valide pour ne pas déconnecter à tort.
     * 
     * @param string $token Token de l'utilisateur
     * @return bool true si le token est encore valide, false sinon
     */
    public function verifyUserToken(string $token): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($this->baseUrl, [
                'wstoken' => $token,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json'
            ]);
            
            $data = $response->json();
            
            // Si Moodle retourne explicitement invalidtoken, le token a été révoqué
            if (isset($data['errorcode']) && str_contains(strtolower($data['errorcode']), 'invalidtoken')) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // En cas d'erreur réseau, on ne déconnecte pas l'utilisateur
            return true;
        }
    }

    /**
     * Récupère les informations de base (dont l'ID et le username) en utilisant le token de l'utilisateur.
     * 
     * @param string $token
     * @return array|null
     */
    public function getSiteInfoByToken(string $token): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($this->baseUrl, [
                'wstoken' => $token,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['userid'])) {
                    return $data;
                }
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Récupère le profil complet de l'utilisateur en utilisant son propre token.
     * 
     * @param string $token
     * @param int $moodleId
     * @return array|null
     */
    public function getUserProfileByToken(string $token, int $moodleId): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($this->baseUrl, [
                'wstoken' => $token,
                'wsfunction' => 'core_user_get_users_by_field',
                'field' => 'id',
                'values[0]' => $moodleId,
                'moodlewsrestformat' => 'json'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !empty($data) && isset($data[0]['id'])) {
                    return $data[0];
                }
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Met à jour le mot de passe d'un utilisateur sur Moodle.
     * 
     * @param int $moodleUserId
     * @param string $newPassword
     * @return void
     * @throws Exception
     */
    public function updateUserPassword(int $moodleUserId, string $newPassword): void
    {
        $this->updateUser($moodleUserId, [
            'password' => $newPassword
        ]);
    }
}

