<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class MoodleEventService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];
    }

    public function getAllEvents(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_get_calendar_events'
            ]);
            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (getAllEvents): ' . $data['message']);
                return [];
            }
            return $data;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAllEvents): ' . $e->getMessage());
            return [];
        }
    }

    public function getEvent($id): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_get_calendar_events',
                'events[eventids][0]' => $id
            ]);
            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();
            if (isset($data['errorcode']) || isset($data['exception']) || empty($data['events'])) {
                Log::error('Moodle API Error (getEvent): ' . json_encode($data));
                return [];
            }
            return $data['events'][0];
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getEvent): ' . $e->getMessage());
            return [];
        }
    }

    public function createEvent(Event $event): bool
{
    try {
        $type = $this->mapTypeToMoodle($event->type);
        $timeduration = $this->calculateDuration($event);

        $new_event = [
            'name' => $event->title,
            'description' => $event->description ?? '',
            'format' => 1,
            'location' => $event->location ?? '',
            'eventtype' => $type,
            'timestart' => strtotime($event->date),
            'timeduration' => $timeduration,
            'visible' => 1,
            'sequence' => 1
        ];

        if ($type === 'course' && $event->course_id) {
            $new_event['courseid'] = (int)$event->course_id;
        } elseif ($type === 'category' && $event->category_id) {
            $new_event['categoryid'] = (int)$event->category_id;
        }

        // Pas de 'repeats' ni 'repeatid' pour la création simple

        $params = array_merge($this->defaultParams, [
            'wsfunction' => 'core_calendar_create_calendar_events',
            'events[0]' => $new_event   // Important : events[0] pour le premier événement
        ]);

        Log::info('Moodle API Request (create): ' . json_encode($params));

        $response = Http::asForm()->post($this->apiUrl, $params);
        $data = $response->json();

        Log::info('Moodle API Response (create): ' . json_encode($data));

        if (isset($data['exception']) || isset($data['errorcode'])) {
            Log::error('Moodle create failed: ' . json_encode($data));
            return false;
        }

        return true;
    } catch (\Exception $e) {
        Log::error('Moodle API Error (createEvent): ' . $e->getMessage());
        return false;
    }
}

    public function updateEvent($id, array $data): bool
    {
        try {
            $type = $this->mapTypeToMoodle($data['type']);
            $timestart = strtotime($data['date']);
            $timeduration = 0;
            if ($data['duration_type'] === 'until' && $data['end_date']) {
                $timeduration = strtotime($data['end_date']) - $timestart;
            } elseif ($data['duration_type'] === 'minutes') {
                $timeduration = $data['duration_minutes'] * 60;
            }
            $repeats = $data['repeat_event'] ? $data['repeat_count'] : 0;

            $updated_event = [
                'eventid' => $id,
                'name' => $data['title'],
                'description' => $data['description'] ?? '',
                'format' => 1,
                'location' => $data['location'] ?? '',
                'eventtype' => $type,
                'timestart' => $timestart,
                'timeduration' => $timeduration,
                'repeats' => $repeats,
                'visible' => 1,
                'sequence' => 1
            ];

            if ($type === 'course') {
                $updated_event['courseid'] = $data['course_id'];
            } elseif ($type === 'category') {
                $updated_event['categoryid'] = (int) $data['category_id'];
            }

            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_update_calendar_events',
                'events' => [$updated_event]
            ]);

            Log::info('Moodle API Request (update): ' . json_encode($params));
            $response = Http::asForm()->post($this->apiUrl, $params);
            $data = $response->json();
            Log::info('Moodle API Response (update): ' . json_encode($data));

            if (isset($data['errorcode']) || isset($data['exception'])) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (updateEvent): ' . $e->getMessage());
            return false;
        }
    }

    public function deleteEvent($id): bool
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_delete_calendar_events',
                'events[0][eventid]' => $id,
                'events[0][repeat]' => 1  // Supprime les répétitions si applicable
            ]);

            Log::info('Moodle API Request (delete): ' . json_encode($params));
            $response = Http::asForm()->post($this->apiUrl, $params);
            $data = $response->json();
            Log::info('Moodle API Response (delete): ' . json_encode($data));

            if (isset($data['errorcode']) || isset($data['exception'])) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (deleteEvent): ' . $e->getMessage());
            return false;
        }
    }

    public function isServerAvailable(): bool
    {
        try {
            $response = Http::get($this->apiUrl, [
                'wstoken' => $this->token,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json'
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Moodle API Error (isServerAvailable): ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Moodle API Error (isServerAvailable): ' . $e->getMessage());
            return false;
}
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

    private function calculateDuration(Event $event): int
    {
        if ($event->duration_type === 'until' && $event->end_date) {
            return strtotime($event->end_date) - strtotime($event->date);
        } elseif ($event->duration_type === 'minutes') {
            return $event->duration_minutes * 60;
        }
        return 0;
    }
}