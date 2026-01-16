<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Section;

class MoodleSectionService
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

        Log::info("Moodle section action logged: {$action}");
    }

    /**
     * Log section creation action
     */
    public function logSectionCreation(Section $section): void
    {
        $sectionData = [
            'id' => $section->id,
            'name' => $section->name,
            'course_id' => $section->course_id,
        ];

        $this->logAction('section_create', $sectionData);
    }

    /**
     * Log section update action
     */
    public function logSectionUpdate(Section $section): void
    {
        $sectionData = [
            'id' => $section->id,
            'name' => $section->name,
            'course_id' => $section->course_id,
        ];

        $this->logAction('section_update', $sectionData);
    }

    /**
     * Log section deletion action
     */
    public function logSectionDeletion(Section $section): void
    {
        $this->logAction('section_delete', ['id' => $section->id]);
    }

    public function listerSectionsCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {

            Log::error('Erreur API Moodle (listerSectionsCours): ' . $e->getMessage(), [
                'file' => storage_path('logs/laravel.log')
            ]);
            Log::error('Erreur API Moodle (listerSectionsCours): ' . $e->getMessage());

            return [];
        }
    }

/**
 * Fonction utilitaire pour transformer un tableau en paramètres d'API REST
 */
protected function rest_api_parameters($in_args, $prefix = '', $out_dict = null)
{
    if ($out_dict === null) {
        $out_dict = [];
    }
    
    if (!is_array($in_args)) {
        $out_dict[$prefix] = $in_args;
        return $out_dict;
    }
    
    if ($prefix == '') {
        $prefix = $prefix . '{0}';
    } else {
        $prefix = $prefix . '[{0}]';
    }
    
    if (array_keys($in_args) === range(0, count($in_args) - 1)) {
        // C'est un tableau
        foreach ($in_args as $idx => $item) {
            $this->rest_api_parameters($item, str_replace('{0}', $idx, $prefix), $out_dict);
        }
    } else {
        // C'est un dictionnaire
        foreach ($in_args as $key => $item) {
            $this->rest_api_parameters($item, str_replace('{0}', $key, $prefix), $out_dict);
        }
    }
    
    return $out_dict;
}



/**
 * Créer une section en utilisant le plugin wsmanagesections
 */
public function creerSection(int $courseId, string $name, int $sectionNumber, array $sectionData = []): array
{
    try {
        // Construire l'URL pour la création
        $url = $this->apiUrl . '?wstoken=' . $this->token 
             . '&wsfunction=local_wsmanagesections_create_sections'
             . '&moodlewsrestformat=json'
             . '&courseid=' . $courseId
             . '&position=' . $sectionNumber
             . '&number=1';
        
        Log::debug('URL de création de section: ' . $url);
        
        $response = Http::get($url);
        
        Log::debug('Réponse brute de création: ' . $response->body());
        
        $result = $response->json();
        
        Log::debug('Résultat JSON de création: ' . json_encode($result));
        
        // Si la section a été créée avec succès, essayons de la renommer
        if (!empty($result) && is_array($result) && isset($result[0]['sectionnumber'])) {
            $sectionNumber = $result[0]['sectionnumber'];
            $sectionId = $result[0]['sectionid'];
            
            Log::debug("Section créée avec succès: ID={$sectionId}, Number={$sectionNumber}");
            
            // Tenter de renommer la section
            $updateUrl = $this->apiUrl . '?wstoken=' . $this->token 
                      . '&wsfunction=local_wsmanagesections_update_sections'
                      . '&moodlewsrestformat=json'
                      . '&courseid=' . $courseId
                      . '&sections[0][type]=num'
                      . '&sections[0][section]=' . $sectionNumber
                      . '&sections[0][name]=' . urlencode($name);
            
            // Ajouter d'autres propriétés si nécessaire
            if (!empty($sectionData['summary'])) {
                $updateUrl .= '&sections[0][summary]=' . urlencode($sectionData['summary']);
            }
            
            if (isset($sectionData['visible'])) {
                $updateUrl .= '&sections[0][visible]=' . $sectionData['visible'];
            }
            
            Log::debug('URL de mise à jour du nom: ' . $updateUrl);
            
            $updateResponse = Http::get($updateUrl);
            
            Log::debug('Réponse brute de mise à jour: ' . $updateResponse->body());
            Log::debug('Code de statut de mise à jour: ' . $updateResponse->status());
        }
        
        return is_array($result) ? $result : [];
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (creerSection): ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return [];
    }
}

/**
 * Modifier une section existante en utilisant le plugin wsmanagesections
 */
public function modifierSection(int $courseId, int $sectionId, array $sectionData): array
{
    try {
        // D'abord obtenir le numéro de section à partir de l'ID
        $sections = $this->listerSectionsCours($courseId);
        $sectionNumber = null;
        
        foreach ($sections as $section) {
            if ($section['id'] == $sectionId) {
                $sectionNumber = $section['section'];
                break;
            }
        }
        
        if ($sectionNumber === null) {
            throw new \Exception("Section ID {$sectionId} non trouvée dans le cours {$courseId}");
        }
        
        // Préparer les données pour la mise à jour
        $sectionsData = [
            [
                'type' => 'num',
                'section' => $sectionNumber,
                'name' => $sectionData['name'] ?? '',
                'summary' => $sectionData['summary'] ?? '',
                'summaryformat' => $sectionData['summaryformat'] ?? 1,
                'visible' => $sectionData['visible'] ?? 1
            ]
        ];
        
        return $this->modifierMultipleSections($courseId, $sectionsData);
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (modifierSection): ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Modifier plusieurs sections
 */
protected function modifierMultipleSections(int $courseId, array $sectionsData): array
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'local_wsmanagesections_update_sections',
            'courseid' => $courseId
        ]);
        
        $sectionsParams = $this->rest_api_parameters(['sections' => $sectionsData]);
        $params = array_merge($params, $sectionsParams);
        
        //$response = Http::post($this->apiUrl, $params);
        $response = Http::get($this->apiUrl . '?' . http_build_query($params));
        return $response->json();
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (modifierMultipleSections): ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Supprimer des sections
 */
public function supprimerSection(int $sectionId): array
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'local_wsmanagesections_delete_sections',
            'sectionids' => [$sectionId]
        ]);
        
        $response = Http::post($this->apiUrl, $params);
        return $response->json();
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (supprimerSection): ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Déplacer une section vers une nouvelle position
 */
public function deplacerSection(int $courseId, int $sectionNumber, int $position): array
{
    try {
        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'local_wsmanagesections_move_section',
            'courseid' => $courseId,
            'sectionnumber' => $sectionNumber,
            'position' => $position
        ]);
        
        $response = Http::post($this->apiUrl, $params);
        return $response->json();
    } catch (\Exception $e) {
        Log::error('Erreur API Moodle (deplacerSection): ' . $e->getMessage());
        throw $e;
    }
}
}