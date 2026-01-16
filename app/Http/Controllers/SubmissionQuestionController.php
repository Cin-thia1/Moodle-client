<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionQuestion;
use App\Models\Question;
use Illuminate\Http\Request;

class SubmissionQuestionController extends Controller
{
    public function store(Request $request, Submission $submission)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.student_answer' => 'required|string',
        ]);

        $submissionQuestions = [];
        foreach ($request->questions as $questionData) {
            $question = Question::findOrFail($questionData['id']);
            $isCorrect = $question->correct_answer === $questionData['student_answer'];

            $submissionQuestions[] = SubmissionQuestion::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'student_answer' => $questionData['student_answer'],
                'is_correct' => $isCorrect,
            ]);
        }

        // Mise à jour des IDs des questions de soumission dans la soumission
        $submission->update(['submission_question_ids' => array_column($submissionQuestions, 'id')]);

        // Calcul du pourcentage de réponses correctes
        $totalQuestions = count($submissionQuestions);
        $correctAnswers = count(array_filter($submissionQuestions, fn($q) => $q->is_correct));
        $percentage = ($correctAnswers / $totalQuestions) * 100;

        return response()->json([
            'message' => 'Les réponses ont été enregistrées.',
            'score' => $percentage . '%',
        ]);
    }
}
