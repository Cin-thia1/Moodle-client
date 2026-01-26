<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\Section;
use App\Models\Module;
use App\Services\MoodleCategoryService;
use App\Services\MoodleCourseService;
use App\Services\MoodleUserService;
use App\Services\MoodleSectionService;
use App\Services\MoodleModuleService;
use App\Services\MoodleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SynchronisationController extends Controller
{
    protected $moodleCourseService;
    protected $moodleUserService;
    protected $moodleCategoryService;
    protected $moodleSectionService;
    protected $moodleModuleService;
    protected $logFilePath;
    protected $moodleAssignmentService;

    public function __construct(
        MoodleCourseService $moodleCourseService,
        MoodleUserService $moodleUserService,
        MoodleCategoryService $moodleCategoryService,
        MoodleSectionService $moodleSectionService,
        MoodleModuleService $moodleModuleService,
        MoodleAssignmentService $moodleAssignmentService
    ) {
        $this->moodleCourseService = $moodleCourseService;
        $this->moodleUserService = $moodleUserService;
        $this->moodleCategoryService = $moodleCategoryService;
        $this->moodleSectionService = $moodleSectionService;
        $this->moodleModuleService = $moodleModuleService;
        $this->moodleAssignmentService = $moodleAssignmentService;
        $this->logFilePath = storage_path('logs/moodle_actions.txt');
    }

    public function synchronize(Request $request)
{
    Log::info('SYNCHRO GLOBALE - Début de la synchronisation', [
        'user_id' => auth()->id() ?? 'guest',
        'time'    => now()->toDateTimeString(),
    ]);

    try {
        $this->checkServerAvailability();

        Log::info('Serveur Moodle OK');

        $this->processLoggedActions();

        Log::info('Actions loguées traitées');

        // 1. Synchro catégories (ton code actuel reste OK)
        $moodleCategories = $this->moodleCategoryService->getToutesCategories();
        $moodleCategoryIds = array_column($moodleCategories, 'id');

        foreach ($moodleCategories as $moodleCategory) {
            $existingCategory = Category::where('moodle_id', $moodleCategory['id'])->first();

            if ($existingCategory) {
                $existingCategory->update(['name' => $moodleCategory['name']]);
            } else {
                $existingCategoryByName = Category::where('moodle_id', null)
                    ->where('name', $moodleCategory['name'])
                    ->first();

                if ($existingCategoryByName) {
                    $existingCategoryByName->update(['moodle_id' => $moodleCategory['id']]);
                } else {
                    Category::create([
                        'moodle_id' => $moodleCategory['id'],
                        'name'      => $moodleCategory['name'],
                    ]);
                }
            }
        }

        Category::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleCategoryIds)->delete();

        Log::info('Catégories synchronisées', ['total' => Category::count()]);

        // 2. Synchro cours → ON GARDE UNIQUEMENT CET APPEL
        Log::info('Avant appel synchronizeCourses()');

$resultCourses = $this->moodleCourseService->synchronizeCourses();

Log::info('Après appel synchronizeCourses()', ['result' => $resultCourses]);

        // 3. Synchro utilisateurs (ton code reste OK)
        $moodleUsers = $this->moodleUserService->getUsers();

        Log::info("NB users moodle reçus", ['count' => count($moodleUsers['users'] ?? [])]);

        foreach ($moodleUsers['users'] as $moodleUser) {
            // ... ton code de création/mise à jour users et rôles reste inchangé
        }

        // 4. Synchro sections + modules + assignments (ton code reste OK)
        $moodleCourses = $this->moodleCourseService->getAllCourses();

        foreach (array_slice($moodleCourses, 1) as $moodleCourse) {
            // ... ton code sections, modules, assignments reste inchangé
        }

        return redirect()->back()->with('success', sprintf(
            'Synchronisation terminée ! %d cours créés, %d mis à jour, %d erreurs.',
            $resultCourses['created'] ?? 0,
            $resultCourses['updated'] ?? 0,
            $resultCourses['errors'] ?? 0
        ));
    } catch (\Exception $e) {
        Log::error('Erreur critique synchronisation globale', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return redirect()->back()->with('error', 'Erreur lors de la synchronisation : ' . $e->getMessage());
    }

    }

    protected function processLoggedActions()
    {
        if (!file_exists($this->logFilePath)) {
            Log::info('Aucun fichier de log à traiter');
            return;
        }

        $file = fopen($this->logFilePath, 'r');
        $tempFilePath = $this->logFilePath . '.temp';
        $tempFile = fopen($tempFilePath, 'w');

        $successfulActions = [];

        while (($line = fgets($file)) !== false) {
            $entry = json_decode($line, true);

            if (!$entry) {
                continue;
            }

            $success = $this->executeAction($entry['action'], $entry['data']);
            Log::info('Action a traitée : ' . $entry['action'] . ' donnee ' . json_encode($entry['data']));

            if ($success) {
                $successfulActions[] = $entry;
                Log::info('success');
            } else {
                fwrite($tempFile, $line);
                Log::info('echec');
            }
        }

        fclose($file);
        fclose($tempFile);

        rename($tempFilePath, $this->logFilePath);

        Log::info('Actions traitées : ' . count($successfulActions) . ' réussies');
    }

    protected function executeAction(string $action, array $data): bool
    {
        try {
            switch ($action) {
                case 'course_create':
                    $course = new Course();
                    $course->fullname = $data['fullname'];
                    $course->shortname = $data['shortname'];
                    $course->category_id = $data['category_id'];
                    $course->summary = $data['summary'];
                    $course->numsections = $data['numsections'];
                    $course->startdate = $data['startdate'];
                    $course->enddate = $data['enddate'];
                    Log::info(' creation du cour avec les donnees data ' . json_encode($data));

                    return $this->moodleCourseService->createCourse($course);

                case 'course_update':
                    $courseData = [
                        'fullname' => $data['fullname'],
                        'shortname' => $data['shortname'],
                        'summary' => $data['summary'],
                        'numsections' => $data['numsections'],
                        'startdate' => strtotime($data['startdate']),
                        'enddate' => $data['enddate'] ? strtotime($data['enddate']) : null,
                    ];

                    $this->moodleCourseService->updateCourse($data['id'], $courseData);
                    return true;

                case 'course_delete':
                    $this->moodleCourseService->deleteCourse($data['id']);
                    return true;

                // Gestion des sections
                case 'section_create':
                    $section = new Section();
                    $section->name = $data['name'];
                    $section->course_id = $data['course_id'];
                    $moodle_id = Course::where('id', $data['course_id'])->value('moodle_id');

                    // Si le moodle_id n'existe pas, on skip la synchronisation
                    if (!$moodle_id) {
                        Log::warning('Cannot create section: course moodle_id is null', ['course_id' => $data['course_id']]);
                        return false;
                    }

                    $sectionNumber = 1;

                    $sectionData = [
                        'summary' => $data['summary'] ?? '',
                        'visible' => $data['visible'] ?? 1
                    ];

                    $result = $this->moodleSectionService->creerSection(
                        $moodle_id,
                        $data['name'],
                        $sectionNumber,
                        $sectionData
                    ) !== null;

                    Log::debug("Résultat de création de section: " . json_encode($result));

                    return !empty($result);

                case 'section_update':
                    $sectionData = [
                        'name' => $data['name'],
                        'visible' => $data['visible'] ?? 1,
                        'summary' => $data['summary'] ?? ''
                    ];

                    return $this->moodleSectionService->modifierSection(
                        $data['course_id'],
                        $data['id'],
                        $sectionData
                    ) !== null;

                case 'section_delete':
                    return $this->moodleSectionService->supprimerSection(
                        $data['id']
                    ) !== null;

                // Gestion des modules
                case 'module_create':
                    $section = Section::find($data['section_id']);
                    if (!$section) {
                        Log::error('Section introuvable', ['section_id' => $data['section_id']]);
                        return false;
                    }

                    $course = $section->course;
                    if (!$course) {
                        Log::error('Cours associé à la section introuvable', ['section_id' => $data['section_id']]);
                        return false;
                    }

                    $moodleId = $course->moodle_id;
                    if (!$moodleId) {
                        Log::error('moodle_id du cours non défini', ['course_id' => $course->id]);
                        return false;
                    }

                    Log::info('Informations récupérées', [
                        'section_id' => $data['section_id'],
                        'course_id' => $course->id,
                        'moodle_id' => $moodleId
                    ]);

                    $moduleData = [
                        'file_path' => $data['file_path'],
                        'modplural' => $data['modplural'] ?? ($data['modname'] . 's'),
                        'downloadcontent' => 1,
                    ];

                    return $this->moodleModuleService->creerModule(
                        $moodleId,
                        $data['modname'],
                        $data['name'],
                        $moduleData
                    ) !== null;

                case 'module_update':
                    $moduleData = [
                        'name' => $data['name'],
                        'modname' => $data['modname'],
                        'modplural' => $data['modplural'] ?? $data['modname'] . 's',
                        'downloadcontent' => $data['downloadcontent'] ?? 0,
                    ];

                    if ($data['modname'] === 'resource' && isset($data['file_path'])) {
                        $moduleData['files'] = $data['file_path'];
                    }

                    // (Ton code d’update/delete module est commenté → je laisse inchangé)
                    // return $this->moodleModuleService->modifierModule(...)

                default:
                    Log::warning("Action inconnue : {$action}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'exécution de l'action {$action} : " . $e->getMessage());
            return false;
        }
    }

    protected function checkServerAvailability()
{
    if (!$this->moodleCourseService->isServerAvailable()) {
        throw new \Exception("Le serveur Moodle n'est pas disponible.");
    }
}
}
