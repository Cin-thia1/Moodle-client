<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\MoodleEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'repeats' => $event->repeat_count - 1,
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
            'repeat_count' => 'required|integer|min:1',
        ]);

        $event = Event::create($validated);

        if ($this->moodleEventService->isServerAvailable()) {
            $created = $this->moodleEventService->createEvent($event);
            if ($created) {
                $moodleId = null;
                if (isset($created['events'][0]['id'])) {
                    $moodleId = $created['events'][0]['id'];
                } elseif (isset($created[0]['id'])) {
                    $moodleId = $created[0]['id'];
                } elseif (isset($created['eventid'])) {
                    $moodleId = $created['eventid'];
                }

                if ($moodleId) {
                    $event->moodle_id = $moodleId;
                    $event->save();
                    return response()->json(['message' => 'Événement créé et synchronisé avec Moodle et enregistré localement.', 'moodle_id' => $moodleId]);
                }

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
    try {
        $source = $request->input('source', 'local');

        // Validation : tous les champs sont "sometimes" (seulement si présents)
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'type' => 'sometimes|required|in:utilisateur,cours,categorie,site',
            'course_id' => 'sometimes|nullable|exists:courses,id',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'description' => 'sometimes|nullable|string',
            'location' => 'sometimes|nullable|string|max:255',
            'duration_type' => 'sometimes|nullable|in:none,until,minutes',
            'end_date' => 'sometimes|nullable|date|after_or_equal:date',
            'duration_minutes' => 'sometimes|nullable|integer|min:1',
            'repeat_event' => 'sometimes|nullable|boolean',
            'repeat_count' => 'sometimes|required|integer|min:1',
        ]);

        if ($source === 'moodle') {
            if (!$this->moodleEventService->isServerAvailable()) {
                return response()->json(['error' => 'Serveur Moodle indisponible.'], 503);
            }

            // Appel direct à updateEvent avec le tableau validé
            $success = $this->moodleEventService->updateEvent($id, $validated);

            return response()->json([
                'message' => $success ? 'Événement mis à jour dans Moodle.' : 'Échec de la mise à jour dans Moodle.',
                'status' => $success ? 200 : 500
            ]);
        }

        // Cas local
        $event = Event::findOrFail($id);

        // Important : merge les données validées avec les anciennes pour ne perdre aucun champ
        $event->update($validated);

       if ($this->moodleEventService->isServerAvailable()) {
            // Si tu as un champ moodle_id dans ta table events (ajoute-le si besoin)
            if ($event->moodle_id) {
                $success = $this->moodleEventService->updateEvent($event->moodle_id, $validated);
                if (!$success) {
                    Log::warning("Échec sync Moodle pour événement local ID {$event->id}");
                }
            } else {
                // Si pas encore sync, on le crée dans Moodle
                $created = $this->moodleEventService->createEvent($event);
                if ($created) {
                    $moodleId = null;
                    if (isset($created['events'][0]['id'])) {
                        $moodleId = $created['events'][0]['id'];
                    } elseif (isset($created[0]['id'])) {
                        $moodleId = $created[0]['id'];
                    } elseif (isset($created['eventid'])) {
                        $moodleId = $created['eventid'];
                    }

                    if ($moodleId) {
                        $event->moodle_id = $moodleId;
                        $event->save();
                    }
                }
            }
        }


        return response()->json([
            'message' => 'Événement mis à jour avec succès.',
            'event' => $event->fresh()
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Meilleur retour d'erreur pour le JS
        return response()->json([
            'error' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Update failed: ' . $e->getMessage());
        return response()->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
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
                'title' => '',
                'date' => '',
                'type' => 'utilisateur',
                'course_id' => null,
                'category_id' => null,
                'description' => '',
                'location' => '',
                'duration_type' => 'none',
                'end_date' => null,
                'duration_minutes' => 0,
                'repeat_event' => false,
                'repeat_count' => 1,
            ];
            $data = array_merge($defaults, $input);
            $data['repeat_event'] = filter_var($data['repeat_event'], FILTER_VALIDATE_BOOLEAN);
            $data['repeat_count'] = max(1, (int) ($data['repeat_count'] ?? 1));
            return $data;
        }

        throw new \InvalidArgumentException('prepareDataForMoodle expects Event or array.');
    }
}