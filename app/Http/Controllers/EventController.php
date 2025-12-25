<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\MoodleEventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $moodleEventService;

    public function __construct(MoodleEventService $moodleEventService)
    {
        $this->moodleEventService = $moodleEventService;
    }

    public function index()
    {
        $localEvents = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'source' => 'local',
                'name' => $event->title,
                'timestart' => strtotime($event->date),
                'eventtype' => $this->mapTypeToMoodle($event->type),
                'description' => $event->description,
                'location' => $event->location,
                'timeduration' => $this->calculateDuration($event),
                'repeats' => $event->repeat_event ? $event->repeat_count : 0,
                'courseid' => $event->course_id,
                'categoryid' => $event->category_id,
            ];
        })->toArray();

        $moodleEvents = [];
        if ($this->moodleEventService->isServerAvailable()) {
            $moodleData = $this->moodleEventService->getAllEvents();
            $moodleEvents = array_map(function ($event) {
                $event['source'] = 'moodle';
                return $event;
            }, $moodleData['events'] ?? []);
        }

        $joinedEvents = array_merge($localEvents, $moodleEvents);
        return response()->json($joinedEvents);
    }

    public function show($id, Request $request)
    {
        $source = $request->input('source', 'local');
        if ($source === 'moodle') {
            return response()->json($this->moodleEventService->getEvent($id));
        } else {
            return response()->json(Event::findOrFail($id));
        }
    }

    public function store(Request $request)
    {
        try{
             $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:utilisateur,cours,categorie,site',
            'course_id' => 'nullable|exists:courses,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'duration_type' => 'nullable|in:none,until,minutes',
            'end_date' => 'nullable|date|after_or_equal:date',
            'duration_minutes' => 'nullable|integer|min:1',
            'repeat_event' => 'nullable|boolean',
            'repeat_count' => 'nullable|integer|min:1',
        ]);

        $event = Event::create($validated);

        if ($this->moodleEventService->isServerAvailable()) {
            $synced = $this->moodleEventService->createEvent($event);
            if ($synced) {
                $event->delete(); // Supprime local après sync réussi
                return response()->json(['message' => 'Événement créé et synchronisé avec Moodle !']);
            }
        }

        return response()->json(['message' => 'Événement créé localement.', 'event' => $event]);
        }catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de la création : ' . $e->getMessage()], 500);
    }
       
    }

    public function update(Request $request, $id)
    {
        try{
            $source = $request->input('source', 'local');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:utilisateur,cours,categorie,site',
            'course_id' => 'nullable|exists:courses,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'duration_type' => 'nullable|in:none,until,minutes',
            'end_date' => 'nullable|date|after_or_equal:date',
            'duration_minutes' => 'nullable|integer|min:1',
            'repeat_event' => 'nullable|boolean',
            'repeat_count' => 'nullable|integer|min:1',
        ]);

        if ($source === 'moodle') {
            $success = $this->moodleEventService->updateEvent($id, $validated);
            return response()->json(['message' => $success ? 'Événement Moodle mis à jour.' : 'Erreur lors de la mise à jour.']);
            /*if ($source === 'moodle') {
    // Reconstruit un faux Event pour passer à updateEvent si besoin, ou modifie la signature
    $fakeEvent = new Event($validated);
    $success = $this->moodleEventService->updateEvent($id, $validated);
    // ...
}*/
        } else {
            $event = Event::findOrFail($id);
            $event->update($validated);
            if ($this->moodleEventService->isServerAvailable()) {
                $this->moodleEventService->createEvent($event); // Resync si possible
                $event->delete();
            }
            return response()->json(['message' => 'Événement mis à jour.', 'event' => $event]);
        }
        }catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        try{
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

    private function mapTypeToMoodle($type)
    {
        return match ($type) {
            'utilisateur' => 'user',
            'cours' => 'course',
            'categorie' => 'category',
            'site' => 'site',
            default => 'user',
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
}