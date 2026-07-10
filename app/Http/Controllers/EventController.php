<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Submission;
use App\Models\QuizAttempt;
use App\Services\MoodleEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $moodleEventService;

    public function __construct(MoodleEventService $moodleEventService)
    {
        $this->moodleEventService = $moodleEventService;
    }

    /**
     * GET /events - Isolation des événements
     */
   public function index()
{
    $user = Auth::user();

    $localEvents = Event::where('date', '>=', now())
        ->where(function ($query) use ($user) {
            // STRICT PERSONAL EVENTS ISOLATION
            // Only the owner can see their personal events (type = utilisateur)
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('type', 'utilisateur');
            })

            // Course events (assignments, quizzes, cours type) → Teacher + Enrolled Students
            ->orWhere(function ($q) use ($user) {
                $q->where('type', 'cours')
                  ->whereHas('course', function ($courseQuery) use ($user) {
                      $courseQuery->where('teacher_id', $user->id)  // Teacher
                                  ->orWhereHas('users', function ($studentQuery) use ($user) {
                                      $studentQuery->where('user_id', $user->id); // Enrolled students
                                  });
                  });
            });
        })
        ->with(['course', 'module'])
        ->orderBy('date', 'asc')
        ->get()
        ->map(function ($event) use ($user) {
            $moodleType = $this->mapTypeToMoodle($event->type);

            return [
                'id'          => $event->id,
                'source'      => 'local',
                'name'        => $event->title,
                'timestart'   => strtotime($event->date),
                'eventtype'   => $moodleType,
                'description' => $event->description ?? '',
                'location'    => $event->location ?? '',
                'timeduration'=> $this->calculateDuration($event),
                'repeats'     => $event->repeat_count - 1,
                'courseid'    => $event->course_id,
                'categoryid'  => $event->category_id,
                'color'       => $this->getEventColor($moodleType),
                'completed'   => $event->date < now(),
                'canEdit'     => $event->user_id == $user->id ||
                                ($event->course && $event->course->teacher_id == $user->id),
                // ✅ Submission tracking fields
                'module_id'   => $event->module_id,
                'module_type' => $event->module ? $event->module->modname : null,
            ];
        })->toArray();

    // Get user's authorized course IDs for Moodle filtering
    if ($user->hasRole('ROLE_TEACHER')) {
        $courseIds = \App\Models\Course::where('teacher_id', $user->id)->pluck('id')->toArray();
    } elseif ($user->hasRole('ROLE_STUDENT')) {
        $courseIds = $user->courses->pluck('id')->toArray();
    } else {
        $courseIds = \App\Models\Course::pluck('id')->toArray();
    }

    // Moodle events (optional - you can filter similarly if needed)
    $moodleEvents = [];
    if ($this->moodleEventService->isServerAvailable()) {
        $moodleData = $this->moodleEventService->getAllEvents();
        $moodleEvents = array_map(function ($event) {
            $type = $event['eventtype'] ?? 'user';
            $event['source'] = 'moodle';
            $event['color'] = $this->getEventColor($type);
            return $event;
        }, $moodleData['events'] ?? []);

        if (!empty($moodleEvents)) {
            $moodleEvents = array_filter($moodleEvents, function ($event) use ($user, $courseIds) {
                $type = $event['eventtype'] ?? 'user';
                if ($type === 'user') {
                    return ($event['userid'] ?? null) == $user->id;
                }
                if ($type === 'course') {
                    return in_array((int)($event['courseid'] ?? 0), $courseIds);
                }
                return true; // Keep category or site events
            });
            $moodleEvents = array_values($moodleEvents);
        }
    }

    return response()->json(array_merge($localEvents, $moodleEvents));
}

    /**
     * Create new event
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'            => 'required|string|max:255',
                'date'             => 'required|date',
                'type'             => 'required|in:utilisateur,cours,categorie,site',
                'course_id'        => 'nullable|exists:courses,id',
                'category_id'      => 'nullable|exists:categories,id',
                'description'      => 'nullable|string',
                'location'         => 'nullable|string|max:255',
                'duration_type'    => 'nullable|in:none,until,minutes',
                'end_date'         => 'nullable|date|after_or_equal:date',
                'duration_minutes' => 'nullable|integer|min:1',
                'repeat_event'     => 'nullable|boolean',
                'repeat_count'     => 'nullable|integer|min:1',
            ]);

            // IMPORTANT: Link to current user for isolation
            $validated['user_id'] = Auth::id();

            $event = Event::create($validated);

            // Moodle synchronization
            if ($this->moodleEventService->isServerAvailable()) {
                $created = $this->moodleEventService->createEvent($event);
                if ($created && isset($created['events'][0]['id'])) {
                    $event->moodle_id = $created['events'][0]['id'];
                    $event->save();
                }
            }

            return response()->json([
                'message' => 'Événement créé avec succès.',
                'event' => $event
            ]);

        } catch (\Exception $e) {
            Log::error('Event creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show single event
     */
    public function show($id, Request $request)
    {
        $source = $request->input('source', 'local');
        if ($source === 'moodle') {
            return response()->json($this->moodleEventService->getEvent($id));
        } else {
            return response()->json(Event::findOrFail($id));
        }
    }

    /**
     * Update event
     */
    public function update(Request $request, $id)
    {
        try {
            $source = $request->input('source', 'local');

            $validated = $request->validate([
                'title'            => 'sometimes|required|string|max:255',
                'date'             => 'sometimes|required|date',
                'type'             => 'sometimes|required|in:utilisateur,cours,categorie,site',
                'course_id'        => 'sometimes|nullable|exists:courses,id',
                'category_id'      => 'sometimes|nullable|exists:categories,id',
                'description'      => 'sometimes|nullable|string',
                'location'         => 'sometimes|nullable|string|max:255',
                'duration_type'    => 'sometimes|nullable|in:none,until,minutes',
                'end_date'         => 'sometimes|nullable|date|after_or_equal:date',
                'duration_minutes' => 'sometimes|nullable|integer|min:1',
                'repeat_event'     => 'sometimes|nullable|boolean',
                'repeat_count'     => 'sometimes|required|integer|min:1',
            ]);

            if ($source === 'moodle') {
                if (!$this->moodleEventService->isServerAvailable()) {
                    return response()->json(['error' => 'Serveur Moodle indisponible.'], 503);
                }
                $success = $this->moodleEventService->updateEvent($id, $validated);
                return response()->json([
                    'message' => $success ? 'Événement mis à jour dans Moodle.' : 'Échec de la mise à jour.',
                    'status' => $success ? 200 : 500
                ]);
            }

            // Local update
            $event = Event::findOrFail($id);
            $event->update($validated);

            // Moodle sync 
            if ($this->moodleEventService->isServerAvailable() && $event->moodle_id) {
                $this->moodleEventService->updateEvent($event->moodle_id, $validated);
            }

            return response()->json([
                'message' => 'Événement mis à jour avec succès.',
                'event' => $event->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete event
     */
    public function destroy($id, Request $request)
    {
        try {
            $source = $request->input('source', 'local');
            if ($source === 'moodle') {
                $success = $this->moodleEventService->deleteEvent($id);
                return response()->json(['message' => $success ? 'Événement Moodle supprimé.' : 'Erreur lors de la suppression.']);
            } else {
                $event = Event::findOrFail($id);
                $event->delete();
                return response()->json(['message' => 'Événement supprimé localement.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    public function getMoodleEvents()
    {
        $events = $this->moodleEventService->getAllEvents();
        return response()->json($events);
    }

    /**
     * GET /events/completion-status
     * Returns the set of module_ids where the current user has:
     *  - submitted an assignment (status = 'submitted')
     *  - finished a quiz attempt (state = 'finished')
     * The frontend uses this to strikethrough those events on the calendar.
     */
    public function completionStatus()
    {
        $user = Auth::user();

        // Assignment submissions — any status that means "submitted"
        $submittedModuleIds = Submission::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'graded', 'draft'])
            ->whereNotNull('submitted_at')
            ->pluck('module_id')
            ->filter()
            ->unique()
            ->values();

        // Finished quiz attempts
        $finishedQuizModuleIds = QuizAttempt::where('user_id', $user->id)
            ->where('state', 'finished')
            ->pluck('module_id')
            ->filter()
            ->unique()
            ->values();

        $completedModuleIds = $submittedModuleIds
            ->merge($finishedQuizModuleIds)
            ->unique()
            ->values();

        return response()->json([
            'completed_module_ids' => $completedModuleIds,
        ]);
    }

    /**
     * Synchronize events with Moodle (bidirectional)
     */
    public function sync(Request $request)
    {
        if (!$this->moodleEventService->isServerAvailable()) {
            return response()->json(['error' => 'Serveur Moodle indisponible.'], 503);
        }

        try {
            $user = Auth::user();
            $syncedCount = 0;

            // 1. Fetch Moodle Events & Update Local
            $moodleData = $this->moodleEventService->getAllEvents();
            $moodleEvents = $moodleData['events'] ?? [];

            foreach ($moodleEvents as $mEvent) {
                $date = date('Y-m-d H:i:s', $mEvent['timestart']);
                $localType = match ($mEvent['eventtype'] ?? 'user') {
                    'user' => 'utilisateur',
                    'course' => 'cours',
                    'category' => 'categorie',
                    'site' => 'site',
                    default => 'utilisateur',
                };

                Event::updateOrCreate(
                    ['moodle_id' => $mEvent['id']],
                    [
                        'title' => $mEvent['name'] ?? 'Événement Moodle',
                        'description' => $mEvent['description'] ?? '',
                        'date' => $date,
                        'type' => $localType,
                        'location' => $mEvent['location'] ?? '',
                        'course_id' => !empty($mEvent['courseid']) ? $mEvent['courseid'] : null,
                        'category_id' => !empty($mEvent['categoryid']) ? $mEvent['categoryid'] : null,
                        'user_id' => $user->id,
                    ]
                );
                $syncedCount++;
            }

            // 2. Fetch Local Events without Moodle ID & Push to Moodle
            // Only push events belonging to the current user to avoid duplicates if other users are active
            $localEvents = Event::whereNull('moodle_id')->where('user_id', $user->id)->get();
            foreach ($localEvents as $localEvent) {
                $created = $this->moodleEventService->createEvent($localEvent);
                if ($created && isset($created['events'][0]['id'])) {
                    $localEvent->moodle_id = $created['events'][0]['id'];
                    $localEvent->save();
                    $syncedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Synchronisation réussie. {$syncedCount} événements traités."
            ]);
        } catch (\Exception $e) {
            Log::error('Event Sync failed: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la synchronisation : ' . $e->getMessage()], 500);
        }
    }

    // ====================== HELPER METHODS ======================

    private function canUserEditEvent($user, $event)
    {
        if ($event->user_id == $user->id) return true;           // Owner
        if ($event->course && $event->course->teacher_id == $user->id) return true; // Teacher
        return false;
    }

    private function mapTypeToMoodle($type)
    {
        return match ($type) {
            'utilisateur' => 'user',
            'cours'       => 'course',
            'categorie'   => 'category',
            'site'        => 'site',
            default       => 'user',
        };
    }

    private function calculateDuration($event)
    {
        if ($event->duration_type === 'until' && $event->end_date) {
            return strtotime($event->end_date) - strtotime($event->date);
        } elseif ($event->duration_type === 'minutes') {
            return $event->duration_minutes * 60;
        }
        return 0;
    }

    private function getEventColor($type)
    {
        return match ($type) {
            'user'      => '#1e88e5',
            'course'    => '#e53935',
            'category'  => '#8e24aa',
            'site'      => '#43a047',
            default     => '#546e7a',
        };
    }

    private function prepareDataForMoodle($input): array
    {
        if ($input instanceof Event) {
            return [
                'title' => $input->title,
                'date' => $input->date,
                'type' => $input->type,
                'course_id' => $input->course_id,
                'category_id' => $input->category_id,
                'description' => $input->description,
                'location' => $input->location,
                'duration_type' => $input->duration_type ?? 'none',
                'end_date' => $input->end_date ?? null,
                'duration_minutes' => $input->duration_minutes ?? 0,
                'repeat_event' => (bool) $input->repeat_event,
                'repeat_count' => max(1, (int) ($input->repeat_count ?? 1)),
            ];
        }

        if (is_array($input)) {
            $defaults = [
                'title' => '', 'date' => '', 'type' => 'utilisateur',
                'course_id' => null, 'category_id' => null, 'description' => '',
                'location' => '', 'duration_type' => 'none', 'end_date' => null,
                'duration_minutes' => 0, 'repeat_event' => false, 'repeat_count' => 1,
            ];
            $data = array_merge($defaults, $input);
            $data['repeat_event'] = filter_var($data['repeat_event'], FILTER_VALIDATE_BOOLEAN);
            $data['repeat_count'] = max(1, (int) ($data['repeat_count'] ?? 1));
            return $data;
        }

        throw new \InvalidArgumentException('prepareDataForMoodle expects Event or array.');
    }
}