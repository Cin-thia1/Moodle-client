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

    public function createEvent(Event $event)
    {
        try {
            $type = match ($event->type) { // English version to crrespond to Moodle API
                'utilisateur' => 'user',
                'cours' => 'course',
                'categorie' => 'category',
                default => 'user',
            };

            $new_event = [
                'name' => $event->title,
                'description' => '',
                'format' => 1,  // HTML format
                'groupid' => null,
                'eventtype' => $type,
                'timestart' => strtotime($event->date),
                'timeduration' => 0, // I've not implemented the end time yet
                'visible' => 1,
                'sequence' => 1
            ];

            if ($type === 'course') {
                $new_event['courseid'] = $event->course_id;
            } else if ($type === 'category') {
                $new_event['categoryid'] = (int) $event->category_id;
            }

            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_create_calendar_events',
                'events' => [
                    $new_event,
                ]
            ]);

            Log::error('Moodle API Request: ' . json_encode($params));

            $response = Http::asForm()->post($this->apiUrl, $params);
            $data = $response->json();

            Log::error('Moodle API Response: ' . json_encode($data));

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (createEvent): ' . json_encode($data));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (createEvent): ' . $e->getMessage());
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
}
