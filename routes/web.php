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
        $courses = Course::all();
        $categories = Category::all();

        return view('dashboard', compact('courses', 'categories'));
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

    // Courses
    Route::resource('courses', CourseController::class);

    // Modules
    Route::get('/modules/download/{module}', [ModuleController::class, 'download'])->name('modules.download');
    Route::post('/synchronisation', [SynchronisationController::class, 'synchronize'])->name('synchronisation');
    //Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    //Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');

    //Sections (nested under courses)
    Route::prefix('/courses/{course}')->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index'); // List all sections of a course
        Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create'); // Form to create a section
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store'); // Store a new section
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show'); // Show a specific section
        Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit'); // Form to edit a section
        Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update'); // Update a section
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy'); // Delete a section
    });

    Route::get('/sections/create_for_teacher/{course_id}', [SectionController::class, 'create_for_teacher'])->name('teachers.sections.create');
    Route::post('/sections/store_for_teacher/', [SectionController::class, 'store_for_teacher'])->name('teachers.sections.store');

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



// Include authentication routes
require __DIR__.'/auth.php';