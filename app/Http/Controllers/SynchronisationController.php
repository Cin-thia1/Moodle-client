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
        try {
            // Vérifier la disponibilité du serveur Moodle
            $this->checkServerAvailability();

            // Exécuter les actions à partir du fichier de log
            $this->processLoggedActions();

            // Synchronisation des catégories
            $moodleCategories = $this->moodleCategoryService->getToutesCategories();
            $moodleCategoryIds = array_column($moodleCategories, 'id');
            foreach ($moodleCategories as $moodleCategory) {
                $existingCategory = Category::where('moodle_id', $moodleCategory['id'])->first();

                if ($existingCategory) {
                    $existingCategory->update([
                        'name' => $moodleCategory['name'],
                    ]);
                } else {
                    $existingCategoryByName = Category::where('moodle_id', null)
                        ->where('name', $moodleCategory['name'])
                        ->first();

                    if ($existingCategoryByName) {
                        $existingCategoryByName->update([
                            'moodle_id' => $moodleCategory['id'],
                        ]);
                    } else {
                        Category::create([
                            'moodle_id' => $moodleCategory['id'],
                            'name' => $moodleCategory['name'],
                        ]);
                    }
                }
            }
            Category::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleCategoryIds)->delete();

            // Synchronisation des cours
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            $moodleCourseIds = array_column($moodleCourses, 'id');

            foreach (array_slice($moodleCourses, 1) as $moodleCourse) {
                $categoryId = Category::where('moodle_id', $moodleCourse['categoryid'])->value('id');

                if (in_array($moodleCourse['categoryid'], $moodleCategoryIds)) {

                    // ✅ Normalisation teacher_id : on essaye de mapper vers l'ID local du user
                    // Si l'enseignant Moodle existe localement, on stocke l'ID local (recommandé)
                    // Sinon on garde la valeur Moodle (fallback)
                    $localTeacherId = null;
                    if (!empty($moodleCourse['teacher_id'])) {
                        $localTeacherId = User::where('moodle_id', $moodleCourse['teacher_id'])->value('id');
                    }
                    $teacherIdToStore = $localTeacherId ?? ($moodleCourse['teacher_id'] ?? null);

                    $existingCourse = Course::where('moodle_id', $moodleCourse['id'])->first();

                    if ($existingCourse) {
                        $existingCourse->update([
                            'fullname' => $moodleCourse['fullname'],
                            'shortname' => $moodleCourse['shortname'],
                            'summary' => $moodleCourse['summary'],
                            'numsections' => $moodleCourse['numsections'],
                            'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                            'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                            'teacher_id' => $teacherIdToStore, // ✅ ici
                            'category_id' => $categoryId,
                            'image' => $moodleCourse['image'] ?? null,
                        ]);
                    } else {
                        $existingCourseByName = Course::where('moodle_id', null)
                            ->where('fullname', $moodleCourse['fullname'])
                            ->first();

                        if ($existingCourseByName) {
                            $existingCourseByName->update([
                                'moodle_id' => $moodleCourse['id'],
                                'shortname' => $moodleCourse['shortname'],
                                'summary' => $moodleCourse['summary'],
                                'numsections' => $moodleCourse['numsections'],
                                'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                                'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                                'teacher_id' => $teacherIdToStore, // ✅ ici
                                'category_id' => $categoryId,
                                'image' => $moodleCourse['image'] ?? null,
                            ]);
                        } else {
                            Course::create([
                                'moodle_id' => $moodleCourse['id'],
                                'fullname' => $moodleCourse['fullname'],
                                'shortname' => $moodleCourse['shortname'],
                                'summary' => $moodleCourse['summary'],
                                'numsections' => $moodleCourse['numsections'],
                                'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                                'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                                'teacher_id' => $teacherIdToStore, // ✅ ici
                                'category_id' => $categoryId,
                                'image' => $moodleCourse['image'] ?? null,
                            ]);
                        }
                    }
                } else {
                    Log::warning('La catégorie avec le moodle_id ' . $moodleCourse['categoryid'] . ' n\'existe pas.');
                }
            }

            Course::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleCourseIds)->delete();

            // ===========================
            // ✅ Synchronisation des utilisateurs (AVEC RÔLES SANS ÉCRASER ROLE_ADMIN)
            // ===========================
            $moodleUsers = $this->moodleUserService->getUsers();
            $moodleUserIds = array_column($moodleUsers['users'], 'id');

            foreach ($moodleUsers['users'] as $moodleUser) {

                $localUser = User::where('moodle_id', $moodleUser['id'])->first();

                if ($localUser) {
                    $localUser->update([
                        'name' => $moodleUser['fullname'],
                        'email' => $moodleUser['email'],
                        'password' => bcrypt('password'),
                        'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                    ]);
                } else {
                    $localUser = User::whereNull('moodle_id')
                        ->where('email', $moodleUser['email'])
                        ->first();

                    if ($localUser) {
                        $localUser->update([
                            'moodle_id' => $moodleUser['id'],
                            'name' => $moodleUser['fullname'],
                            'password' => bcrypt('defaultpassword'),
                            'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                        ]);
                    } else {
                        $localUser = User::create([
                            'moodle_id' => $moodleUser['id'],
                            'name' => $moodleUser['fullname'],
                            'email' => $moodleUser['email'],
                            'password' => bcrypt('defaultpassword'),
                            'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                        ]);
                    }
                }

                // ✅ Déterminer le rôle souhaité
                // Si teacher_id est normalisé (id local), on vérifie avec $localUser->id
                // Sinon fallback : teacher_id peut être l'id moodle (ancienne data)
                $isTeacher = Course::where('teacher_id', $localUser->id)->exists()
                    || Course::where('teacher_id', $moodleUser['id'])->exists();

                $role = $isTeacher ? 'ROLE_TEACHER' : 'ROLE_STUDENT';

                // ✅ Appliquer sans écraser les autres rôles (ex ROLE_ADMIN)
                if ($role === 'ROLE_TEACHER') {
                    if (! $localUser->hasRole('ROLE_TEACHER')) {
                        $localUser->assignRole('ROLE_TEACHER');
                    }
                    if ($localUser->hasRole('ROLE_STUDENT')) {
                        $localUser->removeRole('ROLE_STUDENT');
                    }
                } else {
                    if (! $localUser->hasRole('ROLE_STUDENT')) {
                        $localUser->assignRole('ROLE_STUDENT');
                    }
                    if ($localUser->hasRole('ROLE_TEACHER')) {
                        $localUser->removeRole('ROLE_TEACHER');
                    }
                }

                Log::info("Rôle mis à jour", [
                    'user_id' => $localUser->id,
                    'moodle_user_id' => $moodleUser['id'],
                    'expected_role' => $role,
                    'roles' => $localUser->getRoleNames(),
                ]);
            }

            User::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleUserIds)->delete();

            // Synchronisation des sections depuis Moodle vers le client
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            foreach (array_slice($moodleCourses, 1)  as $moodleCourse) {
                $sections = $this->moodleSectionService->listerSectionsCours($moodleCourse['id']);
                $moodleSectionIds = array_column($sections, 'id');

                foreach ($sections as $section) {
                    $courseId = Course::where('moodle_id', $moodleCourse['id'])->value('id');

                    $existingSection = Section::where('moodle_id', $section['id'])->first();

                    if ($existingSection) {
                        $existingSection->update([
                            'name' => $section['name'],
                            'course_id' => $courseId,
                        ]);
                    } else {
                        $existingSectionByName = Section::where('moodle_id', null)
                            ->where('name', $section['name'])
                            ->where('course_id', $courseId)
                            ->first();

                        if ($existingSectionByName) {
                            $existingSectionByName->update([
                                'moodle_id' => $section['id'],
                            ]);
                        } else {
                            Section::create([
                                'moodle_id' => $section['id'],
                                'name' => $section['name'],
                                'course_id' => $courseId,
                            ]);
                        }
                    }

                    $modules = $section['modules'];
                    $moodleModuleIds = array_column($modules, 'id');

                    foreach ($modules as $module) {
                        $sectionId = Section::where('moodle_id', $section['id'])->value('id');
                        $courseId = Course::where('moodle_id', $moodleCourse['id'])->value('id');

                        $moduleData = [
                            'name' => $module['name'],
                            'modname' => $module['modname'],
                            'modplural' => $module['modplural'] ?? 'Default Value',
                            'downloadcontent' => $module['downloadcontent'] ?? '',
                            'section_id' => $sectionId,
                            'course_id' => $courseId,
                            'file_path' => isset($module['contents'][0]['fileurl'])
                                ? str_replace('?forcedownload=1', '?token='.config('moodle.api_token'), $module['contents'][0]['fileurl'])
                                : '',
                        ];

                        if ($module['modname'] === 'assign') {
                            $assignmentDetails = $this->moodleAssignmentService->getAssignmentDetails(
                                $module['id'],
                                $moodleCourse['id']
                            );

                            Log::info("Détails de l'assignement récupérés", [
                                'course_id' => $moodleCourse['id'],
                                'assignment_details' => $assignmentDetails
                            ]);

                            if ($assignmentDetails) {
                                $moduleData = array_merge($moduleData, [
                                    'assignment_id' => $assignmentDetails['id'],
                                    'intro' => $assignmentDetails['intro'],
                                    'activity' => $assignmentDetails['activity'],
                                    'duedate' => Carbon::createFromTimestamp($assignmentDetails['duedate']),
                                    'allowsubmissionsfromdate' => Carbon::createFromTimestamp($assignmentDetails['allowsubmissionsfromdate']),
                                    'gradingduedate' => Carbon::createFromTimestamp($assignmentDetails['gradingduedate']),
                                    'pdf_filename' => $assignmentDetails['pdf_file']['filename'] ?? null,
                                    'pdf_url' => isset($assignmentDetails['pdf_file']['fileurl'])
                                        ? $assignmentDetails['pdf_file']['fileurl'] . '?token=' . config('moodle.api_token')
                                        : null,
                                ]);
                            }
                        }

                        $existingModule = Module::where('moodle_id', $module['id'])->first();

                        if ($existingModule) {
                            $existingModule->update($moduleData);
                        } else {
                            $existingModuleByName = Module::where('moodle_id', null)
                                ->where('name', $module['name'])
                                ->where('section_id', $sectionId)
                                ->first();

                            if ($existingModuleByName) {
                                $existingModuleByName->update(['moodle_id' => $module['id']] + $moduleData);
                            } else {
                                Module::create(['moodle_id' => $module['id']] + $moduleData);
                            }
                        }

                        try {
                            Log::info("Début de la synchronisation des assignments pour le cours", ['course_id' => $moodleCourse['id']]);
                            $this->moodleAssignmentService->syncAssignmentsWithModules($moodleCourse['id']);
                            Log::info("Synchronisation des assignments terminée pour le cours", ['course_id' => $moodleCourse['id']]);
                        } catch (\Exception $e) {
                            Log::error("Erreur lors de la synchronisation des assignments pour le cours {$moodleCourse['id']}: " . $e->getMessage());
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Synchronisation terminée !');
        }
        catch (\Exception $e) {
            Log::error('Erreur de synchronisation : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de synchronisation : ' . $e->getMessage());
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
            return redirect()->back()->with('alert', 'Le serveur Moodle n\'est pas disponible.');
        }
    }
}
