<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Module;
use App\Services\MoodleSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(MoodleSubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function index($moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $assignment = $module->assignment;
        
        if (!$assignment) {
            return redirect()->back()->with('error', 'No assignment found for this module');
        }

        // Synchroniser avec Moodle
        $submissions = $this->submissionService->getSubmissions($assignment->moodle_id);
        
        return view('assignment-submissions', [
            'module' => $module,
            'submissions' => $submissions,
            'assignment' => $assignment
        ]);
    }

    // public function show(Submission $submission)
    // {
    //     $submissionQuestions = SubmissionQuestion::where('submission_id', $submission->id)->get();
    //     return view('submissions.show', compact('submission', 'submissionQuestions'));
    // }

    public function store(Request $request, $moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $assignment = $module->assignment;
        
        $request->validate([
            'content' => 'required_without:file|string',
            'file' => 'required_without:content|file|mimes:pdf,txt|max:5120'
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions');
        }

        // Créer la soumission locale
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'file_path' => $filePath,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        // Synchroniser avec Moodle
        $success = $this->submissionService->submitAssignment(
            $assignment->moodle_id,
            Auth::user()->moodle_id,
            $request->content,
            $request->file('file')
        );

        if ($success) {
            return redirect()->back()->with('success', 'Submission successful!');
        }

        return redirect()->back()->with('error', 'Failed to submit to Moodle');
    }

    public function grade(Request $request, Submission $submission)
    {
        if (!Auth::user() || !Auth::user()->role === 'ROLE_TEACHER') {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $request->validate([
            'grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'nullable|string'
        ]);

        // Sauvegarder localement
        Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'grade' => $request->grade,
                'comment' => $request->feedback,
                'teacher_id' => Auth::id()
            ]
        );

        $submission->update(['status' => 'graded']);

        // Synchroniser avec Moodle
        $moodleAssignmentId = $submission->assignment->moodle_id;
        $moodleUserId = $submission->user->moodle_id;
        
        $success = $this->submissionService->saveGrade(
            $moodleAssignmentId,
            $moodleUserId,
            $request->grade * 5, // Convertir sur 100
            $request->feedback
        );

        if ($success) {
            return redirect()->back()->with('success', 'Grade saved successfully');
        }

        return redirect()->back()->with('error', 'Grade saved locally but failed to sync with Moodle');
    }
}