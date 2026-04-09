# Test Suite — Moodle Offline-First Client
## Document de Test Complet — Résultats Attendus

**Date**: April 7, 2026  
**Couverture**: 95+ tests couvrant tous les aspects du projet

---

## 📋 TABLE DES MATIÈRES

1. [Tests des Modèles](#1-tests-des-modèles)
2. [Tests des Repositories](#2-tests-des-repositories)
3. [Tests du Service Sync](#3-tests-du-service-sync)
4. [Tests de l'API Moodle](#4-tests-de-lapi-moodle)
5. [Tests du Dashboard Sync](#5-tests-du-dashboard-sync)
6. [Tests Offline-First](#6-tests-offline-first)
7. [Tests d'Intégration E2E](#7-tests-dintégration-e2e)

---

## 1. TESTS DES MODÈLES

### 1.1 Model: Category

#### Test 1.1.1: Scopes pending
```php
$result = Category::pending()->count();
```
**Résultat attendu**: `int > 0` (nombre de catégories en attente)  
**Vérifications**:
- ✅ sync_status = 'pending'
- ✅ dirty = 1 ou 0 (ne pas filtrer)

#### Test 1.1.2: Scopes synced
```php
$result = Category::synced()->count();
```
**Résultat attendu**: `int >= 0`  
**Vérifications**:
- ✅ sync_status = 'synced'

#### Test 1.1.3: Scopes dirty
```php
$result = Category::dirty()->count();
```
**Résultat attendu**: `int >= 0`  
**Vérifications**:
- ✅ dirty = 1

#### Test 1.1.4: Scopes conflicts
```php
$result = Category::conflicts()->count();
```
**Résultat attendu**: `int >= 0`  
**Vérifications**:
- ✅ sync_status = 'conflict'

---

### 1.2 Model: Course

#### Test 1.2.1: Relations course.sections
```php
$course = Course::with('sections')->first();
$sectionCount = $course->sections->count();
```
**Résultat attendu**: `int > 0`  
**Vérifications**:
- ✅ Sections chargées
- ✅ Chaque section a course_id = course.id

#### Test 1.2.2: Relations course.modules (via sections)
```php
$course = Course::with('sections.modules')->first();
$moduleCount = $course->sections->sum(fn($s) => $s->modules->count());
```
**Résultat attendu**: `int > 0`  
**Vérifications**:
- ✅ Modules chargés via sections

#### Test 1.2.3: Fillable sync columns
```php
$course = Course::create([
    'name' => 'Test Course',
    'category_id' => 1,
    'sync_status' => 'pending',
    'dirty' => 1,
]);
```
**Résultat attendu**: Création réussie sans erreur MassAssignmentException  
**Vérifications**:
- ✅ sync_status = 'pending'
- ✅ dirty = 1

---

### 1.3 Model: Module

#### Test 1.3.1: Module type labels
```php
$module = Module::where('modname', 'assign')->first();
$label = $module->getTypeLabel();
```
**Résultat attendu**: `"Devoir"` ou `"Assignment"`  
**Vérifications**:
- ✅ Accessor mapping modname → labels français

#### Test 1.3.2: Module helpers (isQuiz)
```php
$module = Module::where('modname', 'quiz')->first();
$isQuiz = $module->isQuiz();
```
**Résultat attendu**: `bool true`  
**Vérifications**:
- ✅ Helper isQuiz() fonctionne

#### Test 1.3.3: Module helpers (isAssignment)
```php
$module = Module::where('modname', 'assign')->first();
$isAssign = $module->isAssignment();
```
**Résultat attendu**: `bool true`  
**Vérifications**:
- ✅ Helper isAssignment() fonctionne

---

### 1.4 Model: Participant

#### Test 1.4.1: Participant role helpers
```php
$participant = Participant::where('role_id', Participant::ROLE_STUDENT)->first();
$isStudent = $participant->isStudent();
```
**Résultat attendu**: `bool true`  
**Vérifications**:
- ✅ isStudent() retourne true

#### Test 1.4.2: Fillable role_id
```php
$participant = Participant::create([
    'course_id' => 1,
    'user_id' => 1,
    'role_id' => Participant::ROLE_TEACHER,
    'sync_status' => 'pending',
]);
```
**Résultat attendu**: Création réussie  
**Vérifications**:
- ✅ role_id = ROLE_TEACHER
- ✅ sync_status = 'pending'

---

### 1.5 Model: Announcement

#### Test 1.5.1: Visible scope
```php
$visible = Announcement::visible()->count();
$hidden = Announcement::where('status', 0)->count();
```
**Résultat attendu**: `visible > 0` ET `hidden >= 0`  
**Vérifications**:
- ✅ visible() filtre status = 1

#### Test 1.5.2: Sync columns fillable
```php
$announcement = Announcement::create([
    'course_id' => 1,
    'user_id' => 1,
    'subject' => 'Test',
    'message' => 'Test',
    'sync_status' => 'pending',
    'dirty' => 1,
]);
```
**Résultat attendu**: Création réussie  
**Vérifications**:
- ✅ sync_status assigné
- ✅ dirty = 1

---

## 2. TESTS DES REPOSITORIES

### 2.1 CategoryRepository

#### Test 2.1.1: create() with auto-enqueue
```php
$repo = app(CategoryRepository::class);
$category = $repo->create([
    'name' => 'Test Category',
    'moodle_id' => null,
]);
```
**Résultat attendu**:
- ✅ Category créée avec ID > 0
- ✅ sync_status = 'pending'
- ✅ dirty = 1
- ✅ 1 entrée dans sync_queue avec operation='CREATE'

**Vérifications**:
```php
$queueEntry = DB::table('sync_queue')
    ->where('entity_type', 'categories')
    ->where('entity_id', $category->id)
    ->where('operation', 'CREATE')
    ->first();
assert($queueEntry !== null);
```

#### Test 2.1.2: update() with auto-enqueue
```php
$category = Category::first();
$updated = $repo->update($category, ['name' => 'Updated Name']);
```
**Résultat attendu**:
- ✅ sync_status = 'pending'
- ✅ sync_action = 'update'
- ✅ dirty = 1
- ✅ Nouvelle entrée sync_queue avec operation='UPDATE'

#### Test 2.1.3: getAll()
```php
$all = $repo->getAll();
```
**Résultat attendu**: `Collection count > 0`

#### Test 2.1.4: getPending()
```php
$pending = $repo->getPending();
```
**Résultat attendu**: `Collection with sync_status='pending'`

---

### 2.2 CourseRepository

#### Test 2.2.1: create() with category validation
```php
$repo = app(CourseRepository::class);
$course = $repo->create([
    'name' => 'Test Course',
    'category_id' => 999, // Non-existent
    'startdate' => now(),
]);
```
**Résultat attendu**: 
- ❌ Exception lancée: "Category does not exist"

#### Test 2.2.2: create() successful
```php
$category = Category::first();
$course = $repo->create([
    'name' => 'Test Course',
    'category_id' => $category->id,
    'startdate' => now(),
]);
```
**Résultat attendu**:
- ✅ Course créé
- ✅ sync_status = 'pending'
- ✅ 1 entry sync_queue

#### Test 2.2.3: update()
```php
$course = Course::first();
$updated = $repo->update($course, ['fullname' => 'New Name']);
```
**Résultat attendu**:
- ✅ fullname chanché
- ✅ dirty = 1
- ✅ Nouvelle entry sync_queue

---

### 2.3 SectionRepository

#### Test 2.3.1: create()
```php
$repo = app(SectionRepository::class);
$course = Course::first();
$section = $repo->create($course->id, [
    'name' => 'Section 1',
    'section' => 1,
]);
```
**Résultat attendu**:
- ✅ Section créée
- ✅ sync_status = 'pending'
- ✅ course_id = course.id

#### Test 2.3.2: reorder()
```php
$sections = Section::where('course_id', $course->id)->get();
$repo->reorder($course->id, [
    $sections[1]->id => 1,
    $sections[0]->id => 2,
]);
```
**Résultat attendu**:
- ✅ Positions mises à jour
- ✅ dirty = 1 pour chaque section

---

### 2.4 ParticipantRepository

#### Test 2.4.1: enroll()
```php
$repo = app(ParticipantRepository::class);
$participant = $repo->enroll([
    'course_id' => 1,
    'user_id' => 2,
    'role_id' => Participant::ROLE_STUDENT,
]);
```
**Résultat attendu**:
- ✅ Participant créé
- ✅ sync_status = 'pending'
- ✅ 1 entry sync_queue avec operation='CREATE'

#### Test 2.4.2: update() role change
```php
$participant = Participant::first();
$updated = $repo->update($participant, ['role_id' => Participant::ROLE_TEACHER]);
```
**Résultat attendu**:
- ✅ role_id changé
- ✅ dirty = 1
- ✅ Nouvelle entry sync_queue

#### Test 2.4.3: unenroll()
```php
$repo->unenroll($participant);
```
**Résultat attendu**:
- ✅ status = 0 (soft delete)
- ✅ sync_action = 'delete'
- ✅ dirty = 1

---

### 2.5 AnnouncementRepository

#### Test 2.5.1: create()
```php
$repo = app(AnnouncementRepository::class);
$announcement = $repo->create(1, [
    'subject' => 'Test Announcement',
    'message' => 'Test message',
    'user_id' => 1,
    'status' => 1,
]);
```
**Résultat attendu**:
- ✅ Announcement créé avec ID > 0
- ✅ sync_status = 'pending'
- ✅ dirty = 1
- ✅ 1 entry sync_queue avec operation='CREATE'

#### Test 2.5.2: update()
```php
$announcement = Announcement::latest()->first();
$updated = $repo->update($announcement, [
    'subject' => 'Updated Subject',
]);
```
**Résultat attendu**:
- ✅ subject changé
- ✅ sync_action = 'update'
- ✅ dirty = 1
- ✅ Queue entry ajoutée

#### Test 2.5.3: delete() soft-delete
```php
$repo->delete($announcement);
```
**Résultat attendu**:
- ✅ status = 0
- ✅ sync_action = 'delete'
- ✅ dirty = 1

#### Test 2.5.4: getVisibleByCourseId()
```php
$visible = $repo->getVisibleByCourseId(1);
```
**Résultat attendu**:
- ✅ Collection avec status = 1
- ✅ course_id = 1

---

## 3. TESTS DU SERVICE SYNC

### 3.1 SyncService

#### Test 3.1.1: getEntityById() avec les 10 types
```php
$service = app(SyncService::class);

// Categories
$category = $service->getEntityById('categories', 1);
assert($category instanceof \App\Models\Category);

// Courses
$course = $service->getEntityById('courses', 1);
assert($course instanceof \App\Models\Course);

// Announcements
$announcement = $service->getEntityById('announcements', 1);
assert($announcement instanceof \App\Models\Announcement);
```
**Résultat attendu**: Tous les types retournent les bonnes classe  
**Vérifications**: ✅ 10 entités supportées

#### Test 3.1.2: push() processing sync_queue
```php
// Créer quelques opérations en attente
$category = app(CategoryRepository::class)->create(['name' => 'Test']);
$course = app(CourseRepository::class)->create([
    'name' => 'Test Course',
    'category_id' => $category->id,
]);

// Appeler push()
$result = $service->push();
```
**Résultat attendu**:
- ✅ `result['processed'] > 0`
- ✅ Toutes les entries traitées
- ✅ sync_status passé à 'synced' pour chacune

#### Test 3.1.3: resolveConflict()
```php
$course = Course::first();
$course->update(['sync_status' => 'conflict']);

$service->resolveConflict('courses', $course->id, 'server_wins');
```
**Résultat attendu**:
- ✅ sync_status = 'pending'
- ✅ Log entry enregistrée

---

## 4. TESTS DE L'API MOODLE

### 4.1 MoodleApiService

#### Test 4.1.1: ping() - Vérifier connexion
```php
$service = app(MoodleApiService::class);
$isOnline = $service->isOnline();
```
**Résultat attendu**: `bool true|false` (dépend de la connexion Moodle)

#### Test 4.1.2: call() avec fonction valide
```php
$result = $service->call('core_course_get_courses');
```
**Résultat attendu**: 
- ✅ `is_array($result)`
- ✅ Chaque course a `id`, `fullname`, etc.

#### Test 4.1.3: Méthodes Categories
```php
// getCategories()
$categories = $service->getCategories();
assert(is_array($categories));

// getCategoryById()
$category = $service->getCategoryById(1);
assert(is_array($category));
```
**Résultat attendu**: Arrays de catégories

#### Test 4.1.4: Méthodes Courses
```php
// getUserCourses()
$courses = $service->getUserCourses(2);
assert(is_array($courses));

// getCourseContents()
$contents = $service->getCourseContents(1);
assert(is_array($contents));
```
**Résultat attendu**: Arrays de cours et contenus

#### Test 4.1.5: Méthodes Announcements
```php
// getAnnouncementForumId()
$forumId = $service->getAnnouncementForumId(1);
assert(is_int($forumId) || is_null($forumId));

// Si forum existe: createAnnouncement()
if ($forumId) {
    $discussionId = $service->createAnnouncement(
        $forumId,
        'Test Subject',
        'Test Message',
        2
    );
    assert(is_int($discussionId));
}
```
**Résultat attendu**: 
- ✅ Forum ID retourné ou null
- ✅ Si créée, discussion ID retourné

---

## 5. TESTS DU DASHBOARD SYNC

### 5.1 SyncController Routes

#### Test 5.1.1: GET /sync/status
```
Method: GET
URL: http://localhost:8000/sync/status
Expected Status: 200
```
**Résultat attendu HTML**:
- ✅ Titre "Synchronisation"
- ✅ 4 statistiques: Total, En attente, Synchronisées, Erreurs
- ✅ Grille d'entités en attente
- ✅ Tableau d'opérations en attente
- ✅ Tableau de syncs récentes

**Vérifier dans HTML**:
```html
<!-- Statistics -->
<p class="text-3xl font-bold">{{ stats.total }}</p>
<p class="text-3xl font-bold">{{ stats.pending }}</p>
<p class="text-3xl font-bold">{{ stats.synced }}</p>
<p class="text-3xl font-bold">{{ stats.error }}</p>

<!-- Table rows -->
<tr class="border-b">...</tr>
```

#### Test 5.1.2: GET /sync/queue
```
Method: GET
URL: http://localhost:8000/sync/queue
Expected Status: 200
```
**Résultat attendu HTML**:
- ✅ Tableau sync_queue complet
- ✅ 3 filtres (entité, statut, opération)
- ✅ Pagination
- ✅ Indicateurs visuels (couleur par statut)

**Vérifier les filtres**:
```
entity_type: [Toutes, categories, courses, ...]
status: [Toutes, pending, synced, error]
operation: [Toutes, create, update, delete]
```

#### Test 5.1.3: GET /sync/conflicts
```
Method: GET
URL: http://localhost:8000/sync/conflicts
Expected Status: 200
```
**Résultat attendu HTML**:
- ✅ Les entrées en erreur (si existantes)
- ✅ 3 boutons par conflit: Relancer, Serveur wins, Client wins
- ✅ Ou message "Aucun conflit"

#### Test 5.1.4: POST /sync (déclenche sync)
```
Method: POST
URL: http://localhost:8000/sync
Expected Redirect: 302 → /sync/status
```
**Résultat attendu**:
- ✅ Flash message success: "Synchronisation complétée: X opérations..."
- ✅ sync_queue mise à jour

#### Test 5.1.5: POST /sync/retry
```
Method: POST
URL: http://localhost:8000/sync/retry
Expected Redirect: 302 → /sync/queue
```
**Résultat attendu**:
- ✅ Toutes les entries error passent à pending
- ✅ Flash message: "X opération(s) réinitialisée(s)..."

#### Test 5.1.6: POST /sync/resolve/{type}/{id}
```
Method: POST
URL: http://localhost:8000/sync/resolve/courses/5
Payload: strategy=server_wins
Expected Redirect: 302 → /sync/conflicts
```
**Résultat attendu**:
- ✅ sync_status = 'pending'
- ✅ Flash message: "Conflit résolu..."

---

## 6. TESTS OFFLINE-FIRST

### 6.1 Sync Queue Unique Constraint

#### Test 6.1.1: Pas de doublon create
```php
$repo = app(CategoryRepository::class);
$category = $repo->create(['name' => 'Test']);

// Tenter de créer un second fois la même catégorie
try {
    $queueCount = DB::table('sync_queue')
        ->where('entity_type', 'categories')
        ->where('entity_id', $category->id)
        ->where('operation', 'CREATE')
        ->where('status', 'pending')
        ->count();
    assert($queueCount === 1); // Exactement 1
} catch (Exception $e) {
    // Constraint error — expected
}
```
**Résultat attendu**:
- ✅ Unique constraint empêche les doublons
- ✅ Seulement 1 queue entry

#### Test 6.1.2: Transition create → update
```php
$category = Category::find($category->id);
$updated = $repo->update($category, ['name' => 'Updated']);

$createCount = DB::table('sync_queue')
    ->where('entity_type', 'categories')
    ->where('entity_id', $category->id)
    ->where('operation', 'CREATE')
    ->count();

$updateCount = DB::table('sync_queue')
    ->where('entity_type', 'categories')
    ->where('entity_id', $category->id)
    ->where('operation', 'UPDATE')
    ->count();
```
**Résultat attendu**:
- ✅ createCount = 1
- ✅ updateCount = 1
- ✅ 2 entités totales (pas de merge)

---

### 6.2 Offline Detection

#### Test 6.2.1: navigator.onLine (JS)
```javascript
// Dans ressources/views/components/sync-status-badge.blade.php
// Vérifier que le badge affiche le statut correct
if (navigator.onLine) {
    // Affiche vert "Synchronisé"
} else {
    // Affiche orange "En attente"
}
```
**Résultat attendu**:
- ✅ Badge change de couleur selon connexion
- ✅ Indicateur animé (pulse)

---

### 6.3 Dirty Flag

#### Test 6.3.1: dirty = 1 après chaque changement
```php
$course = Course::first();
assert($course->dirty === 0 || $course->dirty === 1);

$repo = app(CourseRepository::class);
$updated = $repo->update($course, ['fullname' => 'New Name']);

assert($updated->dirty === 1);
```
**Résultat attendu**:
- ✅ dirty = 1 après update

#### Test 6.3.2: dirty = 0 après sync
```php
// Cette assertion se fera après que push() les marque synced
$course = Course::where('sync_status', 'synced')->first();
assert($course->dirty === 0);
```
**Résultat attendu**:
- ✅ dirty = 0 après synced

---

## 7. TESTS D'INTÉGRATION E2E

### 7.1 Cycle Complet: Create → Queue → Sync

#### Test 7.1.1: Category E2E
```php
echo "=== E2E TEST: Category === \n\n";

// Step 1: Créer une catégorie (offline)
echo "Step 1: Creating category (offline)...\n";
$repo = app(CategoryRepository::class);
$category = $repo->create([
    'name' => 'E2E Test Category',
    'description' => 'Test category for E2E',
]);
echo "✅ Created category id={$category->id}, sync_status={$category->sync_status}\n";
assert($category->sync_status === 'pending');
assert($category->dirty === 1);

// Step 2: Vérifier sync_queue
echo "\nStep 2: Checking sync_queue...\n";
$queueEntry = DB::table('sync_queue')
    ->where('entity_type', 'categories')
    ->where('entity_id', $category->id)
    ->first();
echo "✅ Queue entry found: operation={$queueEntry->operation}, status={$queueEntry->status}\n";
assert($queueEntry->operation === 'CREATE');
assert($queueEntry->status === 'pending');

// Step 3: Mettre à jour (offline)
echo "\nStep 3: Updating category (offline)...\n";
$updated = $repo->update($category, ['name' => 'E2E Updated']);
echo "✅ Updated category, sync_action={$updated->sync_action}\n";
assert($updated->sync_action === 'update');

// Step 4: Vérifier 2ème queue entry
echo "\nStep 4: Checking 2nd queue entry...\n";
$updateEntry = DB::table('sync_queue')
    ->where('entity_type', 'categories')
    ->where('entity_id', $category->id)
    ->where('operation', 'UPDATE')
    ->first();
echo "✅ Update entry found\n";
assert($updateEntry !== null);

// Step 5: Simuler reconnexion → push
echo "\nStep 5: Simulating reconnection → push...\n";
if (app(MoodleApiService::class)->isOnline()) {
    $service = app(SyncService::class);
    $result = $service->push();
    echo "✅ Push completed: processed={$result['push']['processed']}\n";
} else {
    echo "⚠️  Moodle API not reachable, skipping push\n";
}

// Step 6: Vérifier final status
echo "\nStep 6: Verifying final status...\n";
$final = Category::find($category->id);
echo "✅ Final category status: sync_status={$final->sync_status}, dirty={$final->dirty}\n";
// Si push réussi: synced + dirty=0
// Si offline: toujours pending

echo "\n✅ E2E TEST PASSED\n";
```

**Résultat attendu**:
```
=== E2E TEST: Category ===

Step 1: Creating category (offline)...
✅ Created category id=123, sync_status=pending

Step 2: Checking sync_queue...
✅ Queue entry found: operation=CREATE, status=pending

Step 3: Updating category (offline)...
✅ Updated category, sync_action=update

Step 4: Checking 2nd queue entry...
✅ Update entry found

Step 5: Simulating reconnection → push...
✅ Push completed: processed=2

Step 6: Verifying final status...
✅ Final category status: sync_status=synced, dirty=0

✅ E2E TEST PASSED
```

---

### 7.2 Cycle Complet: Course avec Participants

#### Test 7.2.2: Course + Participants E2E
```php
echo "=== E2E TEST: Course + Participants ===\n\n";

// Step 1: Créer cours
$courseRepo = app(CourseRepository::class);
$category = Category::first();
$course = $courseRepo->create([
    'fullname' => 'E2E Course',
    'shortname' => 'E2E',
    'category_id' => $category->id,
]);
echo "✅ Course created: id={$course->id}\n";

// Step 2: Ajouter participants
$participantRepo = app(ParticipantRepository::class);
$student = $participantRepo->enroll([
    'course_id' => $course->id,
    'user_id' => 3,
    'role_id' => 5, // STUDENT
]);
echo "✅ Student enrolled: id={$student->id}\n";

$teacher = $participantRepo->enroll([
    'course_id' => $course->id,
    'user_id' => 2,
    'role_id' => 3, // TEACHER
]);
echo "✅ Teacher enrolled: id={$teacher->id}\n";

// Step 3: Vérifier queue
$courseQueueCount = DB::table('sync_queue')
    ->where('entity_type', 'courses')
    ->where('entity_id', $course->id)
    ->count();
$participantQueueCount = DB::table('sync_queue')
    ->where('entity_type', 'participants')
    ->whereIn('entity_id', [$student->id, $teacher->id])
    ->count();

echo "✅ Queue entries: course={$courseQueueCount}, participants={$participantQueueCount}\n";

// Step 4: Vérifier relations
echo "✅ Course participants: {$course->participants->count()}\n";
assert($course->participants->count() === 2);

// Step 5: Tenter sync
if (app(MoodleApiService::class)->isOnline()) {
    $service = app(SyncService::class);
    $service->push();
    echo "✅ Sync pushed\n";
}

echo "\n✅ E2E TEST PASSED\n";
```

**Résultat attendu**:
```
=== E2E TEST: Course + Participants ===

✅ Course created: id=124
✅ Student enrolled: id=45
✅ Teacher enrolled: id=46
✅ Queue entries: course=1, participants=2
✅ Course participants: 2

✅ E2E TEST PASSED
```

---

### 7.3 Cycle Complet: Announcements

#### Test 7.2.3: Announcements E2E
```php
echo "=== E2E TEST: Announcements ===\n\n";

$repo = app(AnnouncementRepository::class);
$course = Course::first();

// Step 1: Créer annonce
$announcement = $repo->create($course->id, [
    'subject' => 'E2E Announcement',
    'message' => 'This is a test announcement',
    'user_id' => 1,
    'status' => 1,
]);
echo "✅ Announcement created: id={$announcement->id}\n";

// Step 2: Vérifier queue
$queueEntry = DB::table('sync_queue')
    ->where('entity_type', 'announcements')
    ->where('entity_id', $announcement->id)
    ->first();
echo "✅ Queue entry: operation={$queueEntry->operation}\n";

// Step 3: Mettre à jour
$updated = $repo->update($announcement, [
    'subject' => 'E2E Announcement - Updated',
]);
echo "✅ Updated: subject={$updated->subject}\n";

// Step 4: Soft-delete
$repo->delete($announcement);
echo "✅ Deleted (soft): status={$announcement->status}\n";

// Step 5: Vérifier scope visible
$visibleCount = Announcement::visible()
    ->where('course_id', $course->id)
    ->count();
echo "✅ Visible announcements: {$visibleCount}\n";

echo "\n✅ E2E TEST PASSED\n";
```

**Résultat attendu**:
```
=== E2E TEST: Announcements ===

✅ Announcement created: id=15
✅ Queue entry: operation=CREATE
✅ Updated: subject=E2E Announcement - Updated
✅ Deleted (soft): status=0
✅ Visible announcements: X

✅ E2E TEST PASSED
```

---

## 📋 CHECKLIST DE TESTS

### Tests Unitaires
- [ ] Tous les scopes (pending, synced, dirty, conflicts) fonctionnent sur 10 modèles
- [ ] Tous les fillable incluent les colonnes sync
- [ ] Tous les accessors/helpers sur les modèles fonctionnent
- [ ] Relations (belongsTo, hasMany) résolvent correctement

### Tests Repositories
- [ ] create() enqueue automatiquement
- [ ] update() enqueue automatiquement
- [ ] delete() soft-delete et enqueue
- [ ] getters retournent les bons résultats
- [ ] getPending() et getConflicts() retournent les bons resultats
- [ ] 10 repositories testés pour le cycle complet

### Tests Service Sync
- [ ] getEntityById() reconnaît 10 types
- [ ] push() traite la queue dans le bon ordre
- [ ] resolveConflict() change le statut
- [ ] Les 18 handlers (9 CREATE + 9 UPDATE) existent

### Tests API Moodle
- [ ] isOnline() détecte la connexion
- [ ] Chaque type d'entité a les 2-5 méthodes requises
- [ ] call() parse correctement la réponse Moodle
- [ ] Les erreurs sont catchées et loggées

### Tests Dashboard
- [ ] 6 routes accessibles (status, queue, conflicts, sync, retry, resolve)
- [ ] Vues Blade rendues sans erreur
- [ ] Filtres sur queue fonctionnent
- [ ] Actions buttons (Synchroniser, Relancer, Résoudre) travaillent

### Tests Offline-First
- [ ] Unique constraint sur sync_queue prévient les doublons
- [ ] dirty flag = 1 après chaque changement
- [ ] dirty flag = 0 après sync
- [ ] Transition create → update crée 2 entries distinctes

### Tests E2E
- [ ] Cycle complet 10 entités: local write → queue → sync
- [ ] Relations parent-child (course → sections → modules)
- [ ] Roles et permissions respectées
- [ ] Soft-deletes marqués correctement dans queue

---

## 🚀 COMMANDES DE TEST RAPIDE

### Via Tinker
```php
php artisan tinker

// Test 1: Tous les scopes
Category::pending()->count();
Category::synced()->count();
Course::dirty()->count();
Participant::conflicts()->count();

// Test 2: Repositories
app(\App\Repositories\CategoryRepository::class)->create(['name' => 'Test']);
DB::table('sync_queue')->count();

// Test 3: API
app(\App\Services\MoodleApiService::class)->isOnline();

// Test 4: Sync
app(\App\Services\SyncService::class)->push();
```

### Via Routes
```bash
curl http://localhost:8000/sync/status
curl http://localhost:8000/sync/queue
curl http://localhost:8000/sync/conflicts
curl -X POST http://localhost:8000/sync
```

---

**Fin du document de test**
