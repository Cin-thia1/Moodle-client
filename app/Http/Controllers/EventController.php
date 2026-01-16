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
        $localEvents = Event::all();
        $moodleEvents = $this->moodleEventService->getAllEvents()['events'];

        $joinedEvents = array_merge($localEvents->toArray(), $moodleEvents);

        return response()->json($joinedEvents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:utilisateur,cours,categorie,site',
            'course_id' => 'nullable|exists:courses,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'date' => $request->date,
            'type' => $request->type,
            'course_id' => $request->type === 'cours' ? $request->course_id : null,
            'category_id' => $request->type === 'categorie' ? $request->category_id : null,
        ]);

        if ($this->moodleEventService->isServerAvailable()) {
            $this->moodleEventService->createEvent($event);
            $event->delete();
        }

        return response()->json(['message' => 'Event created successfully and synced with Moodle!', 'event' => $event]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    public function getMoodleEvents()
    {
        $events = $this->moodleEventService->getAllEvents();
        return response()->json($events);
    }
}
