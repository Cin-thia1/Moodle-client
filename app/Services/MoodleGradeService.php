<?php

namespace App\Services;

use App\Models\GradeItem;
use App\Models\Grade;
use App\Models\Course;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MoodleGradeService
{
    protected MoodleWebService $webService;
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;

    public function __construct(MoodleWebService $webService = null)
    {
        $this->webService = $webService ?? new MoodleWebService();
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];
    }

    /**
     * Synchronise les critères d'évaluation d'un cours depuis Moodle
     */
    public function syncCourseGradeItems(int $courseId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            $grades = $this->webService->getGrades($course->moodle_id);

            $synced = 0;
            $errors = 0;

            // Structure: table => tabledata (array of grade items)
            if (isset($grades['table'])) {
                $tableData = $grades['table'] ?? [];
                
                if (is_array($tableData)) {
                    foreach ($tableData as $index => $row) {
                        try {
                            $gradeItem = GradeItem::updateOrCreate(
                                ['moodle_id' => $row['itemid'] ?? null],
                                [
                                    'course_id' => $courseId,
                                    'item_name' => $row['itemname'] ?? 'Évaluation ' . ($index + 1),
                                    'item_type' => $row['itemtype'] ?? 'assignment',
                                    'grade_max' => $row['grademax'] ?? 100,
                                    'sort_order' => $index,
                                    'status' => 1,
                                ]
                            );
                            $synced++;
                        } catch (Exception $e) {
                            Log::error("GradeItem sync error: " . $e->getMessage());
                            $errors++;
                        }
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            Log::error("Grade service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Synchronise les notes d'un utilisateur pour un cours
     */
    public function syncUserGrades(int $courseId, int $userId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            if (!$course->moodle_id) {
                throw new Exception("Course not linked to Moodle");
            }

            $grades = $this->webService->getGrades($course->moodle_id, $userId);

            $synced = 0;
            $errors = 0;

            if (isset($grades['table'])) {
                $tableData = $grades['table'] ?? [];

                if (is_array($tableData)) {
                    foreach ($tableData as $row) {
                        try {
                            // Récupère le critère d'évaluation
                            $gradeItem = GradeItem::where('course_id', $courseId)
                                ->where('moodle_id', $row['itemid'] ?? null)
                                ->first();

                            if ($gradeItem && isset($row['grade'])) {
                                $grade = Grade::updateOrCreate(
                                    [
                                        'user_id' => $userId,
                                        'grade_item_id' => $gradeItem->id,
                                    ],
                                    [
                                        'grade_value' => $row['grade'],
                                        'feedback' => $row['feedback'] ?? null,
                                        'graded_at' => now(),
                                    ]
                                );
                                $synced++;
                            }
                        } catch (Exception $e) {
                            Log::error("Grade sync error: " . $e->getMessage());
                            $errors++;
                        }
                    }
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (Exception $e) {
            Log::error("Grade service error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les critères d'évaluation d'un cours
     */
    public function getCourseGradeItems(int $courseId)
    {
        return GradeItem::forCourse($courseId)
            ->visible()
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Récupère les notes d'un utilisateur pour un cours
     */
    public function getUserGrades(int $courseId, int $userId)
    {
        return Grade::where('user_id', $userId)
            ->whereHas('gradeItem', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with('gradeItem')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Récupère les statistiques de notes pour un critère
     */
    public function getGradeStatistics(int $gradeItemId)
    {
        $grades = Grade::where('grade_item_id', $gradeItemId)
            ->pluck('grade_value')
            ->filter();

        if ($grades->isEmpty()) {
            return null;
        }

        return [
            'moyenne' => round($grades->avg(), 2),
            'min' => $grades->min(),
            'max' => $grades->max(),
            'median' => $this->calculateMedian($grades->toArray()),
            'count' => $grades->count(),
        ];
    }

    /**
     * Calcule la médiane
     */
    private function calculateMedian(array $values): float|int
    {
        sort($values);
        $count = count($values);
        $middle = intval($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    /**
     * Récupérer toutes les notes pour un utilisateur dans un cours (compatible legacy)
     */
    public function getNotesPourUtilisateur(int $userId, int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_user_get_grades_table',
                'courseid' => $courseId,
                'userid' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getNotesPourUtilisateur): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les notes de tous les étudiants pour un cours
     */
    public function getNotesDuCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_overview_get_course_grades',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getNotesDuCours): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour une note pour un utilisateur
     */
    public function mettreAJourNote(int $gradeid, float $nouveauNote, string $feedback = ''): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_grades_update_grades',
                'gradeid' => $gradeid,
                'grade' => $nouveauNote
            ]);

            // Ajouter un commentaire si fourni
            if (!empty($feedback)) {
                $params['feedback'] = $feedback;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (mettreAJourNote): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculer la moyenne générale d'un utilisateur
     */
    public function calculerMoyenneGenerale(int $userId, int $courseId): float
    {
        try {
            $notes = $this->getNotesPourUtilisateur($userId, $courseId);
            
            // Extraire et calculer la moyenne
            $totalNotes = 0;
            $nombreNotes = 0;

            foreach ($notes['tables'][0]['tabledata'] as $item) {
                if (isset($item['grade']['content'])) {
                    $note = floatval(str_replace(',', '.', $item['grade']['content']));
                    $totalNotes += $note;
                    $nombreNotes++;
                }
            }

            return $nombreNotes > 0 ? $totalNotes / $nombreNotes : 0;
        } catch (\Exception $e) {
            Log::error('Erreur de calcul de moyenne (calculerMoyenneGenerale): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Exporter les notes d'un cours
     */
    public function exporterNotes(int $courseId, string $format = 'csv'): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_export_get_export_action',
                'courseid' => $courseId,
                'format' => $format
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (exporterNotes): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier le bulletin de notes d'un utilisateur
     */
    public function verifierBulletinNotes(int $userId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'gradereport_overview_get_grade_overview',
                'userid' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (verifierBulletinNotes): ' . $e->getMessage());
            throw $e;
        }
    }
}