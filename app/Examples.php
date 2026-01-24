<?php

/**
 * Exemples d'utilisation des 5 sections
 */

// ============ 1. ANNONCES ============

use App\Models\Announcement;
use App\Models\Course;
use App\Services\MoodleAnnouncementService;
use App\Services\MoodleWebService;

// Synchroniser les annonces d'un cours
$course = Course::find(1);
$service = new MoodleAnnouncementService(new MoodleWebService());
$result = $service->syncCourseAnnouncements($course->id);
// Résultat: ['synced' => 5, 'errors' => 0]

// Créer une annonce
$announcement = $service->createAnnouncement(
    courseId: 1,
    userId: auth()->id(),
    subject: 'Bienvenue au cours!',
    message: 'Ceci est l\'annonce d\'accueil du cours...'
);

// Récupérer les annonces visibles
$announcements = $service->getCourseAnnouncements(1);
foreach ($announcements as $ann) {
    echo $ann->subject . "\n";
}

// ============ 2. DOCUMENTS ============

use App\Services\MoodleDocumentService;

$docService = new MoodleDocumentService(new MoodleWebService());

// Synchroniser les documents d'un cours
$result = $docService->syncCourseDocuments(1);

// Créer un document
$document = $docService->createDocument(
    courseId: 1,
    userId: auth()->id(),
    filename: 'Chapitre1.pdf',
    data: [
        'filepath' => '/uploads/',
        'mimetype' => 'application/pdf',
        'filesize' => 2048576,
        'file_url' => 'https://example.com/files/chapter1.pdf',
    ]
);

// Lister les documents
$docs = $docService->getCourseDocuments(1);

// ============ 3. PARTICIPANTS ============

use App\Models\Participant;
use App\Services\MoodleParticipantService;

$partService = new MoodleParticipantService(new MoodleWebService());

// Synchroniser les participants
$result = $partService->syncCourseParticipants(1);

// Enrôler un utilisateur
$participant = $partService->enrollUser(
    courseId: 1,
    userId: 5,
    role: Participant::ROLE_STUDENT
);

// Récupérer les enseignants d'un cours
$teachers = $partService->getParticipantsByRole(1, Participant::ROLE_TEACHER);

// Changer le rôle
$partService->changeRole(1, 5, Participant::ROLE_TEACHER);

// Désenrôler
$partService->unenrollUser(1, 5);

// ============ 4. NOTES ============

use App\Services\MoodleGradeService;
use App\Models\GradeItem;

$gradeService = new MoodleGradeService(new MoodleWebService());

// Synchroniser les critères d'évaluation
$result = $gradeService->syncCourseGradeItems(1);

// Synchroniser les notes d'un utilisateur
$result = $gradeService->syncUserGrades(1, 5);

// Récupérer les critères d'un cours
$items = $gradeService->getCourseGradeItems(1);
foreach ($items as $item) {
    echo $item->item_name . " (Max: " . $item->grade_max . ")\n";
}

// Récupérer les notes d'un utilisateur
$grades = $gradeService->getUserGrades(1, 5);
foreach ($grades as $grade) {
    echo $grade->gradeItem->item_name . ": " . $grade->grade_value . "\n";
}

// Statistiques pour un critère
$stats = $gradeService->getGradeStatistics(3);
echo "Moyenne: " . $stats['moyenne'] . "\n";
echo "Min: " . $stats['min'] . ", Max: " . $stats['max'] . "\n";

// ============ 5. COMPÉTENCES ============

use App\Services\MoodleCompetencyService;
use App\Models\Competency;
use App\Models\UserCompetency;

$compService = new MoodleCompetencyService(new MoodleWebService());

// Synchroniser les compétences d'un cours
$result = $compService->syncCourseCompetencies(1);

// Synchroniser les compétences d'un utilisateur
$result = $compService->syncUserCompetencies(5);

// Créer une compétence
$competency = $compService->createCompetency([
    'shortname' => 'php-basics',
    'idnumber' => 'PHP001',
    'description' => 'Bases de PHP',
]);

// Récupérer les compétences d'un cours
$courseComps = $compService->getCourseCompetencies(1);

// Récupérer les compétences d'un utilisateur
$userComps = $compService->getUserCompetencies(5);
foreach ($userComps as $comp) {
    echo $comp->competency->shortname . ": ";
    echo ($comp->isComplete() ? "Complète" : "Incomplète") . "\n";
}

// Compétences complétées uniquement
$completed = $compService->getUserCompletedCompetencies(5);

// Marquer une compétence comme complète
$compService->updateUserCompetency(5, 1, [
    'proficiency' => UserCompetency::PROFICIENCY_COMPLETE,
    'reviewed_at' => now(),
]);

// Statistiques pour une compétence
$stats = $compService->getCompetencyStatistics(1);
echo "Complétée par: {$stats['completed']}/{$stats['total']} ({$stats['percentage']}%)\n";

// ============ PERMISSIONS ============

// Vérifier une permission
if (auth()->user()->can('view_announcements')) {
    // Afficher les annonces
}

// Vérifier un rôle
if (auth()->user()->hasRole(Participant::ROLE_TEACHER)) {
    // Afficher l'interface enseignant
}

// Assigner un rôle
$user->assignRole('ROLE_TEACHER');

// Retirer un rôle
$user->removeRole('ROLE_TEACHER');

// ============ REQUÊTES ÉLOQUENT ============

// Annonces visibles d'un cours
$announcements = Announcement::forCourse(1)->visible()->get();

// Participants actifs d'un cours
$participants = Participant::forCourse(1)->active()->get();

// Participants par rôle
$teachers = Participant::forCourse(1)->byRole(Participant::ROLE_TEACHER)->get();

// Compétences actives
$competencies = Competency::active()->get();

// Compétences complétées d'un utilisateur
$completed = UserCompetency::where('user_id', 5)->complete()->get();
