<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubmissionQuestionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CompetencyController;
use App\Http\Controllers\SynchronisationController;
use App\Models\Category;
use App\Models\Course;
use App\Http\Controllers\WelcomeController;


// Welcome route (accessible sans authentification)
Route::get('/', [WelcomeController::class, 'index'])->name('home');


// Group of routes requiring authentication
Route::middleware('auth')->group(function () {
// Dashboard
Route::get('/dashboard', function () {
    $user = Auth::user();

    // Tous les cours (comme avant)
    $courses = Course::all();
    $categories = Category::all();

    // Charger les devoirs à venir (pour la chronologie)
    $assignments = App\Models\Module::where('modname', 'assign')
        ->where('duedate', '>=', now()) // seulement les devoirs futurs
        ->orderBy('duedate', 'asc')
        ->get();

    return view('dashboard', compact('courses', 'categories', 'assignments'));
})->middleware(['verified'])->name('dashboard');
    // Profile management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Administration (requires ROLE_ADMIN)
    Route::prefix('admin')->middleware('role:ROLE_ADMIN')->group(function () {
        Route::get('/create-user', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/create-user', [AdminController::class, 'store'])->name('admin.store');
    });

    // Assignments
    Route::resource('assignments', AssignmentController::class);
    Route::post('/assignments/{assignment}/toggle-publish', [AssignmentController::class, 'togglePublish'])
    ->name('assignments.togglePublish');
    // Routes pour les questions d'un assignment
    Route::get('assignments/{assignment}/questions/edit', [AssignmentController::class, 'editQuestions'])->name('assignments.questions.edit');
    Route::put('assignments/{assignment}/questions/update', [AssignmentController::class, 'updateQuestions'])->name('assignments.questions.update');

    //oweh
Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
Route::get('/assignments/{id}', [AssignmentController::class, 'show'])->name('assignments.show');
Route::get('/courses/{courseId}/gradebook', [AssignmentController::class, 'gradebook'])->name('courses.gradebook');

//ajouter un devoir
Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
Route::resource('assignments', AssignmentController::class)
    ->parameters(['assignments' => 'module']);



    // Courses
    Route::resource('courses', CourseController::class);
    // Teacher dashboard (management view)
    Route::get('/courses/{course}/teacher-dashboard', [CourseController::class, 'show'])->name('courses.teacher-dashboard');

    // Modules
    Route::get('/modules/download/{module}', [ModuleController::class, 'download'])->name('modules.download');
    Route::post('/synchronisation', [SynchronisationController::class, 'synchronize'])->name('synchronisation');
    //Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    //Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');

    // questions d'une soumission
    // Routes pour les soumissions
    Route::resource('submissions', SubmissionController::class);

    // Routes pour les questions de soumission
    Route::post('submissions/{submission}/questions', [SubmissionQuestionController::class, 'store'])->name('submissions.questions.store');
    // Grades
    Route::resource('grades', GradeController::class);
    Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('submissions.grade');

    // Users
    Route::resource('users', UserController::class);

    //Categories
    Route::resource('categories', CategoryController::class);

    // Route pour obtenir les cours d'une catégorie
    Route::get('categories/{id}/courses', [CategoryController::class, 'getCourses']);

    // Routes pour les administrateurs
    Route::middleware(['auth', 'role:ROLE_ADMIN'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Routes pour les questions
    Route::resource('/questions', QuestionController::class);

    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    // Update (PUT/PATCH) and Delete
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::patch('/events/{id}', [EventController::class, 'update'])->name('events.update.patch');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');;

    // Contact
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
});
    Route::resource('modules', ModuleController::class);

// About
Route::view('/about', 'about')->name('about');

Route::middleware(['auth', 'role:ROLE_STUDENT'])->group(function () {
    Route::get('assignments/{assignment}/compose', [AssignmentController::class, 'compose'])
        ->name('assignments.compose');
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])
        ->name('assignments.submit');
});

Route::get('/download/module/{module}', [ModuleController::class, 'download'])
     ->name('module.download');


     /// ////////////// Assignments Routes for Students
Route::resource('assignments', AssignmentController::class)->only(['show']);
Route::post('assignments/{module}/compose', [AssignmentController::class, 'composeTest'])
    ->name('assignments.compose');
Route::post('assignments/{module}/grades', [AssignmentController::class, 'createGrade'])
    ->name('grades.create');

Route::get('/assignments/{module}/submissions', [AssignmentController::class, 'submissions'])
    ->name('assignments.submissions');

// Preview the fake assignments list
Route::get('/mock-assignments', function () {
    return view('mock-assignments');
})->name('mock.assignments');

// Preview the fake submissions list
Route::get('/mock-submissions', function () {
    return view('mock-submissions');
})->name('mock.submissions');

// Include authentication routes
require __DIR__.'/auth.php';


//routes pour notes
Route::middleware(['auth'])->group(function () {
    Route::patch('/assignments/{module}/grade/{student}', [AssignmentController::class, 'saveGrade'])
        ->name('assignments.grade');
});

Route::patch('/courses/{courseId}/gradebook/save', [AssignmentController::class, 'saveGradebook'])
    ->name('gradebook.save');

Route::post('/assignments/{moduleId}/submit', [AssignmentController::class, 'submit'])
    ->name('assignments.submit');

// Routes pour les 5 sections principales
Route::middleware(['auth'])->group(function () {
    // Annonces
    Route::prefix('courses/{course}/announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::patch('/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::post('/sync', [AnnouncementController::class, 'sync'])->name('announcements.sync');
    });

    // Documents
    Route::prefix('courses/{course}/documents')->group(function () {
        Route::get('/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::patch('/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        Route::post('/sync', [DocumentController::class, 'sync'])->name('documents.sync');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    });

    // Global document routes (not course-specific)
    Route::get('documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');

    // Participants
    Route::prefix('courses/{course}/participants')->group(function () {
        Route::get('/', [ParticipantController::class, 'index'])->name('participants.index');
        Route::get('/create', [ParticipantController::class, 'create'])->name('participants.create');
        Route::post('/', [ParticipantController::class, 'store'])->name('participants.store');
        Route::get('/{participant}/edit', [ParticipantController::class, 'edit'])->name('participants.edit');
        Route::patch('/{participant}', [ParticipantController::class, 'update'])->name('participants.update');
        Route::delete('/{participant}', [ParticipantController::class, 'destroy'])->name('participants.destroy');
        Route::post('/sync', [ParticipantController::class, 'sync'])->name('participants.sync');
        Route::get('/by-role/{role}', [ParticipantController::class, 'byRole'])->name('participants.byRole');
    });

    // Sections
    Route::prefix('courses/{course}/sections')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('sections.index');
        Route::get('/create', [SectionController::class, 'create'])->name('sections.create');
        Route::post('/', [SectionController::class, 'store'])->name('sections.store');
        Route::get('/{section}', [SectionController::class, 'show'])->name('sections.show');
        Route::get('/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
        Route::patch('/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
    });

    // Notes (Grades)
    Route::prefix('courses/{course}/grades')->group(function () {
        Route::get('/items', [GradeController::class, 'courseItems'])->name('grades.items');
        Route::get('/gradebook', [GradeController::class, 'courseGradebook'])->name('grades.gradebook');
        Route::get('/user', [GradeController::class, 'userGrades'])->name('grades.user');
        Route::get('/item/{gradeItem}/statistics', [GradeController::class, 'itemStatistics'])->name('grades.itemStatistics');
        Route::post('/sync-items', [GradeController::class, 'syncItems'])->name('grades.syncItems');
        Route::post('/sync-user/{userId}', [GradeController::class, 'syncUserGrades'])->name('grades.syncUserGrades');
    });

    // Compétences
    Route::prefix('competencies')->group(function () {
        Route::get('/', [CompetencyController::class, 'index'])->name('competencies.index');
        Route::get('/create', [CompetencyController::class, 'create'])->name('competencies.create');
        Route::post('/', [CompetencyController::class, 'store'])->name('competencies.store');
        Route::get('/{competency}/edit', [CompetencyController::class, 'edit'])->name('competencies.edit');
        Route::patch('/{competency}', [CompetencyController::class, 'update'])->name('competencies.update');
        Route::get('/{competency}/statistics', [CompetencyController::class, 'statistics'])->name('competencies.statistics');
        Route::get('/user/{userId}', [CompetencyController::class, 'userCompetencies'])->name('competencies.userCompetencies');
        Route::get('/user/{userId}/completed', [CompetencyController::class, 'userCompletedCompetencies'])->name('competencies.userCompleted');
        Route::post('/user/{userId}/mark-complete/{competencyId}', [CompetencyController::class, 'markComplete'])->name('competencies.markComplete');
    });

    Route::prefix('courses/{course}/competencies')->group(function () {
        Route::get('/', [CompetencyController::class, 'courseCompetencies'])->name('competencies.course');
        Route::post('/sync', [CompetencyController::class, 'syncCourse'])->name('competencies.syncCourse');
    });

    Route::post('/users/{userId}/competencies/sync', [CompetencyController::class, 'syncUser'])->name('competencies.syncUser');

    // API Routes for Documents
    Route::prefix('api/courses/{course}/documents')->group(function () {
        Route::post('/', [DocumentApiController::class, 'store'])->name('api.documents.store');
    });
    
    Route::prefix('api/documents')->group(function () {
        Route::delete('/{document}', [DocumentApiController::class, 'destroy'])->name('api.documents.destroy');
        Route::get('/{document}/download', [DocumentApiController::class, 'download'])->name('api.documents.download');
        Route::get('/{document}/preview', [DocumentApiController::class, 'preview'])->name('api.documents.preview');
    });
});
