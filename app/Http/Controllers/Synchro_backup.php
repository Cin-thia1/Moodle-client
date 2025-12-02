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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SynchronisationController extends Controller
{
    protected $moodleCourseService;
    protected $moodleUserService;
    protected $moodleCategoryService;
    protected $moodleSectionService;
    protected $moodleModuleService;
    protected $logFilePath;

    public function __construct(
        MoodleCourseService $moodleCourseService,
        MoodleUserService $moodleUserService,
        MoodleCategoryService $moodleCategoryService,
        MoodleSectionService $moodleSectionService,
        MoodleModuleService $moodleModuleService
    ) {
        $this->moodleCourseService = $moodleCourseService;
        $this->moodleUserService = $moodleUserService;
        $this->moodleCategoryService = $moodleCategoryService;
        $this->moodleSectionService = $moodleSectionService;
        $this->moodleModuleService = $moodleModuleService;
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
                // Vérifiez si une catégorie avec le même moodle_id existe déjà
                $existingCategory = Category::where('moodle_id', $moodleCategory['id'])->first();

                if ($existingCategory) {
                    // Mettre à jour l'enregistrement existant
                    $existingCategory->update([
                        'name' => $moodleCategory['name'],
                    ]);
                } else {
                    // Si moodle_id est null mais name correspond, mettre à jour
                    $existingCategoryByName = Category::where('moodle_id', null)
                        ->where('name', $moodleCategory['name'])
                        ->first();

                    if ($existingCategoryByName) {
                        $existingCategoryByName->update([
                            'moodle_id' => $moodleCategory['id'],
                        ]);
                    } else {
                        // Créer un nouvel enregistrement
                        Category::create([
                             'moodle_id' => $moodleCategory['id'],
                            'name' => $moodleCategory['name'],
                        ]);
                    }
                }
            }
            // Supprimer les catégories pour lesquelles moodle_id n'est pas null mais qui ne sont pas retrouvées dans Moodle
            Category::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleCategoryIds)->delete();

            // Synchronisation des cours
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            $moodleCourseIds = array_column($moodleCourses, 'id');
            foreach (array_slice($moodleCourses, 1) as $moodleCourse) {
                 // Récupérer l'ID de la catégorie locale correspondant au moodle_id de la catégorie Moodle
                $categoryId = Category::where('moodle_id', $moodleCourse['categoryid'])->value('id');

                // Vérifiez que la catégorie existe avant de mettre à jour le cours
                if (in_array($moodleCourse['categoryid'], $moodleCategoryIds)) {
                     // Vérifiez si un cours avec le même moodle_id existe déjà
                $existingCourse = Course::where('moodle_id', $moodleCourse['id'])->first();

                if ($existingCourse) {
                    // Mettre à jour l'enregistrement existant
                    $existingCourse->update([
                        'fullname' => $moodleCourse['fullname'],
                        'shortname' => $moodleCourse['shortname'],
                        'summary' => $moodleCourse['summary'],
                        'numsections' => $moodleCourse['numsections'],
                        'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                        'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                        'teacher_id' => $moodleCourse['teacher_id'] ?? null,
                        'category_id' => $categoryId,
                        'image' => $moodleCourse['image'] ?? null,
                    ]);
                } else {
                    // Si moodle_id est null mais fullname correspond, mettre à jour
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
                            'teacher_id' => $moodleCourse['teacher_id'] ?? null,
                            'category_id' => $categoryId,
                            'image' => $moodleCourse['image'] ?? null,
                        ]);
                    } else {
                        // Créer un nouvel enregistrement
                        Course::create([
                            'moodle_id' => $moodleCourse['id'],
                            'fullname' => $moodleCourse['fullname'],
                            'shortname' => $moodleCourse['shortname'],
                            'summary' => $moodleCourse['summary'],
                            'numsections' => $moodleCourse['numsections'],
                            'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                            'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                            'teacher_id' => $moodleCourse['teacher_id'] ?? null,
                            'category_id' => $categoryId,
                            'image' => $moodleCourse['image'] ?? null,
                        ]);
                    }
                }
                } else {
                    Log::warning('La catégorie avec le moodle_id ' . $moodleCourse['categoryid'] . ' n\'existe pas.');
                }
            }
            // Supprimer les cours pour lesquels moodle_id n'est pas null mais qui ne sont pas retrouvés dans Moodle
            Course::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleCourseIds)->delete();

            //Synchronisation des utilisateurs
            $moodleUsers = $this->moodleUserService->getUsers();
            $moodleUserIds = array_column($moodleUsers['users'], 'id');
            foreach ($moodleUsers['users'] as $moodleUser) {
                 $existingUser = User::where('moodle_id', $moodleUser['id'])->first();

                if ($existingUser) {
                    $existingUser->update([
                        'name' => $moodleUser['fullname'],
                        'email' => $moodleUser['email'],
                        'password' => bcrypt('password'),
                        'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                    ]);
                } else {
                    $existingUserByEmail = User::where('moodle_id', null)
                        ->where('email', $moodleUser['email'])
                        ->first();

                    if ($existingUserByEmail) {
                        $existingUserByEmail->update([
                            'moodle_id' => $moodleUser['id'],
                            'name' => $moodleUser['fullname'],
                            'password' => bcrypt('defaultpassword'),
                            'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                        ]);
                    } else {
                        User::create([
                                'moodle_id' => $moodleUser['id'],
                                'name' => $moodleUser['fullname'],
                                'email' => $moodleUser['email'],
                                'password' => bcrypt('defaultpassword'),
                                'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                            ]);
                    }
                }
            }
            // Supprimer les utilisateurs pour lesquels moodle_id n'est pas null mais qui ne sont pas retrouvés dans Moodle
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

                        $existingModule = Module::where('moodle_id', $module['id'])->first();

                        if ($existingModule) {
                            $existingModule->update([
                                'name' => $module['name'],
                                'modname' => $module['modname'],
                                'modplural' => $module['modplural'] ?? 'Default Value',
                                'downloadcontent' => $module['downloadcontent'] ?? '', 
                                'file_path' => isset($module['contents'][0]['fileurl']) 
                                                    ? str_replace('?forcedownload=1', '?token=' . config('moodle.api_token'), $module['contents'][0]['fileurl']) 
                                                    : '',
                                'section_id' => $sectionId,
                                'course_id' => $courseId,
                            ]);
                        } else {
                            $existingModuleByName = Module::where('moodle_id', null)
                                ->where('name', $module['name'])
                                ->where('section_id', $sectionId)
                                ->first();

                            if ($existingModuleByName) {
                                $existingModuleByName->update([
                                    'moodle_id' => $module['id'],
                                ]);
                            } else {
                                Module::create([
                                    'moodle_id' => $module['id'],
                                    'name' => $module['name'],
                                    'modname' => $module['modname'],
                                    'modplural' => $module['modplural'] ?? 'Default Value',
                                    'downloadcontent' => $module['downloadcontent'] ?? '', 
                                    'file_path' => isset($module['contents'][0]['fileurl']) 
                                                        ? str_replace('?forcedownload=1', '?token=' . config('moodle.api_token'), $module['contents'][0]['fileurl']) 
                                                        : '', 
                                    'section_id' => $sectionId,
                                    'course_id' => $courseId,
                                ]);
                            }
                        }
                    }
                }
            }
                

//            Section::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleSectionIds)->delete();
//            Module::whereNotNull('moodle_id')->whereNotIn('moodle_id', $moodleModuleIds)->delete();


            return redirect()->back()->with('success', 'Synchronisation terminée !');
        } 
        catch (\Exception $e) {
            Log::error('Erreur de synchronisation : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de synchronisation : ' . $e->getMessage());
        }
    }
    
    /**
     * Process actions logged in the file
     */
    protected function processLoggedActions()
    {
        if (!file_exists($this->logFilePath)) {
            Log::info('Aucun fichier de log à traiter');
            return;
        }

        // Lire le fichier ligne par ligne
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
                // Écrire les actions échouées dans le fichier temporaire
                fwrite($tempFile, $line);
                Log::info('echec');
            }
        }
        
        fclose($file);
        fclose($tempFile);
        
        // Remplacer l'ancien fichier par le nouveau
        rename($tempFilePath, $this->logFilePath);
        
        Log::info('Actions traitées : ' . count($successfulActions) . ' réussies');
    }

    /**
     * Execute a specific action
     */
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
                    
                default:
                    Log::warning("Action inconnue : {$action}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'exécution de l'action {$action} : " . $e->getMessage());
            return false;
        }
    }

    protected function checkServerAvailability() {        
        // Vérifier la disponibilité du serveur Moodle
        if (!$this->moodleCourseService->isServerAvailable()) {
            return redirect()->back()->with('alert', 'Le serveur Moodle n\'est pas disponible.');
        }
    }
}
