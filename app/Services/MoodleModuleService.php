<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Module;

class MoodleModuleService
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

        Log::info("Moodle module action logged: {$action}");
    }

    /**
     * Log module creation action
     */
    public function logModuleCreation(Module $module): void
    {
        $moduleData = [
            'id' => $module->id,
            'name' => $module->name,
            'modname' => $module->modname,
            'section_id' => $module->section_id,
            'file_path' => $module->file_path,
        ];

        $this->logAction('module_create', $moduleData);
    }

    /**
     * Log module update action
     */
    public function logModuleUpdate(Module $module): void
    {
        $moduleData = [
            'id' => $module->id,
            'name' => $module->name,
            'modname' => $module->modname,
            'section_id' => $module->section_id,
            'course_id' => $module->course_id,
        ];

        $this->logAction('module_update', $moduleData);
    }

    /**
     * Log module deletion action
     */
    public function logModuleDeletion(Module $module): void
    {
        $this->logAction('module_delete', ['id' => $module->id]);
    }

    public function listerModulesCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (listerModulesCours): ' . $e->getMessage());
            return [];
        }
    }

    public function creerModule(int $courseId, string $moduleType, string $name, array $additionalParams = []): array
    {
        try {

        // 1. Vérifier si un fichier doit être téléversé
        $filePath = $additionalParams['file_path'] ?? null;
        $fileItemId = null;

        if ($filePath && file_exists(storage_path('app/public/' . $filePath))) {
            // 2. Téléverser le fichier vers Moodle
            $fileItemId = $this->uploadFileMoodle($courseId, storage_path('app/public/' . $filePath));
            Log::info('Fichier téléversé vers Moodle', ['itemid' => $fileItemId]);
        }

        // 3. Construire les paramètres pour la création du module
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_module',
                'coursemodule[course]' => $courseId,
                'coursemodule[module]' => $moduleType,
                'coursemodule[name]' => $name
            ]);

            foreach ($additionalParams as $key => $value) {
                $params["coursemodule[$key]"] = $value;
            }

            Log::info('Appel API Moodle (creerModule)', ['params' => $params]);
            $response = Http::get($this->apiUrl, $params);

            if (!$response->successful()) {
            Log::error('Échec API Moodle', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [];
        }
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (creerModule): ' . $e->getMessage());
            throw $e;
        }
    }

// Nouvelle fonction pour téléverser un fichier vers Moodle
private function uploadFileMoodle(int $courseId, string $filePath): ?int
{
    try {
        $fileName = basename($filePath);
        
        // 1. Créer un draftid pour le fichier
        $draftIdResponse = Http::get($this->apiUrl, array_merge($this->defaultParams, [
            'wsfunction' => 'core_user_create_user_file_drafts',
            'draftfiles[0][filepath]' => '/',
            'draftfiles[0][filename]' => $fileName,
        ]));
        
        if (!$draftIdResponse->successful()) {
            Log::error('Échec création draft Moodle', [
                'status' => $draftIdResponse->status(),
                'body' => $draftIdResponse->body()
            ]);
            return null;
        }
        
        $draftId = $draftIdResponse->json()['draftid'] ?? null;
        if (!$draftId) {
            Log::error('Draft ID non trouvé dans la réponse');
            return null;
        }
        
        // 2. Téléverser le fichier
        $fileContents = file_get_contents($filePath);
        $fileResponse = Http::attach(
            'file', $fileContents, $fileName
        )->post($this->apiUrl, array_merge($this->defaultParams, [
            'wsfunction' => 'core_files_upload',
            'itemid' => $draftId,
            'component' => 'user',
            'filearea' => 'draft',
            'filepath' => '/',
            'filename' => $fileName,
        ]));
        
        if (!$fileResponse->successful()) {
            Log::error('Échec téléversement fichier Moodle', [
                'status' => $fileResponse->status(),
                'body' => $fileResponse->body()
            ]);
            return null;
        }
        
        return $draftId;
    } catch (\Exception $e) {
        Log::error('Erreur téléversement fichier Moodle: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return null;
    }
}
}