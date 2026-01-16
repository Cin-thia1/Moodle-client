<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Submission;

class MoodleSubmissionService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
    }

    public function getSubmissions($assignmentId)
    {
        try {
            $response = Http::get($this->apiUrl, [
                'wstoken' => $this->token,
                'wsfunction' => 'mod_assign_get_submissions',
                'assignmentids[0]' => $assignmentId,
                'moodlewsrestformat' => 'json'
            ]);

            $data = $response->json();
            
            // Logique pour traiter la réponse et synchroniser avec la base locale
            if (isset($data['assignments'][0]['submissions'])) {
                return $this->syncLocalSubmissions($data['assignments'][0]['submissions'], $assignmentId);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Error fetching submissions: " . $e->getMessage());
            return [];
        }
    }

    protected function syncLocalSubmissions(array $moodleSubmissions, $assignmentId)
    {
        $syncedSubmissions = [];
        
        foreach ($moodleSubmissions as $submission) {
            $user = User::where('moodle_id', $submission['userid'])->first();
            
            if (!$user) continue;
            
            $localSubmission = Submission::updateOrCreate(
                [
                    'assignment_id' => $assignmentId,
                    'user_id' => $user->id,
                    'attempt_number' => $submission['attemptnumber']
                ],
                [
                    'status' => $submission['status'],
                    'submitted_at' => $submission['timemodified'] ? now()->setTimestamp($submission['timemodified']) : null,
                ]
            );

            // Traitement du contenu texte
            foreach ($submission['plugins'] as $plugin) {
                if ($plugin['type'] === 'onlinetext' && isset($plugin['editorfields'][0]['text'])) {
                    $localSubmission->content = $plugin['editorfields'][0]['text'];
                    $localSubmission->save();
                }
            }

            $syncedSubmissions[] = $localSubmission;
        }

        return $syncedSubmissions;
    }

    public function saveGrade($assignmentId, $userId, $grade, $feedback)
    {
        try {
            $response = Http::post($this->apiUrl, [
                'wstoken' => $this->token,
                'wsfunction' => 'mod_assign_save_grade',
                'assignmentid' => $assignmentId,
                'userid' => $userId,
                'grade' => $grade,
                'plugindata[assignfeedback_comments][text]' => $feedback,
                'moodlewsrestformat' => 'json'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error saving grade: " . $e->getMessage());
            return false;
        }
    }

    public function submitAssignment($assignmentId, $userId, $textContent = null, $fileContent = null)
    {
        try {
            $params = [
                'wstoken' => $this->token,
                'wsfunction' => 'mod_assign_save_submission',
                'assignmentid' => $assignmentId,
                'moodlewsrestformat' => 'json'
            ];

            if ($textContent) {
                $params['plugindata[onlinetext_editor][text]'] = $textContent;
                $params['plugindata[onlinetext_editor][format]'] = 1; // HTML format
            }

            // TODO: Gestion des fichiers (nécessite upload séparé)
            // if ($fileContent) {
            //     $draftId = $this->getDraftFileArea();
            //     $this->uploadFile($draftId, $fileContent);
            //     $params['plugindata[file][draftid]'] = $draftId;
            // }

            $response = Http::post($this->apiUrl, $params);

            // Soumettre pour notation
            if ($response->successful()) {
                $submitResponse = Http::post($this->apiUrl, [
                    'wstoken' => $this->token,
                    'wsfunction' => 'mod_assign_submit_for_grading',
                    'assignmentid' => $assignmentId,
                    'acceptsubmissionstatement' => 1,
                    'moodlewsrestformat' => 'json'
                ]);

                return $submitResponse->successful();
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Error submitting assignment: " . $e->getMessage());
            return false;
        }
    }
}