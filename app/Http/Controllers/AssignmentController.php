<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\AssignmentFile;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a paginated list of assignments or submissions (tabbed view)
     */
    public function index(Request $request)
    {
        $view = $request->query('view', 'assignments');

        if ($view === 'submissions') {
            $submissions = Submission::with(['student', 'assignment', 'grade'])
                ->latest()
                ->paginate(15);

            return view('assignments.index', compact('submissions'));
        }

        // Default: assignments list
        $assignments = Module::where('modname', 'assign')
            ->with(['section', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    
/**
 * Show the form for creating a new assignment
 */
public function create()
{
    // Fetch all modules (or filter them as needed)
    $modules = \App\Models\Module::orderBy('name')->get();

    // If you also want questions (optional - comment out if table doesn't exist)
    // $questions = \App\Models\Question::orderBy('content')->get() ?? collect();

    // Pass to view
    return view('assignments.create', compact('modules'));
}

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after:now',
            'max_grade'   => 'required|integer|min:1|max:100',
            'file'        => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = [
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'due_date'    => $validated['due_date'],
            'max_grade'   => $validated['max_grade'],
            'modname'     => 'assign',
            'created_by'  => Auth::id(),
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('assignments', 'public');
            $data['file_path'] = $path;
        }

        $module = Module::create($data);

        return redirect()
            ->route('assignments.show', $module)
            ->with('success', 'Énoncé créé avec succès.');
    }

    /**
     * Display the specified assignment
     */
    public function show(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        $module->load([
            'submissions' => fn($q) => $q->with(['user', 'grade', 'files'])->latest()
        ]);

        return view('assignments.show', compact('module'));
    }

    /**
     * Show the form for editing the assignment
     */
    public function edit(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        return view('assignments.edit', compact('module'));
    }


    /**
     * Update the specified assignment
     */
    public function update(Request $request, Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after:now',
            'max_grade'   => 'required|integer|min:1|max:100',
            'file'        => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $validated;

        if ($request->hasFile('file')) {
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }
            $data['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $module->update($data);

        return redirect()
            ->route('assignments.show', $module)
            ->with('success', 'Énoncé mis à jour.');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        if (!Auth::user()->hasRole('ROLE_TEACHER')) {
            return back()->with('error', 'Action non autorisée.');
        }

        if ($module->submissions()->exists()) {
            return back()->with('error', 'Impossible de supprimer : des soumissions existent.');
        }

        if ($module->file_path) {
            Storage::disk('public')->delete($module->file_path);
        }

        $module->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Énoncé supprimé.');
    }

    /**
     * List submissions for a module (alternative route if needed)
     */
    public function submissions($moduleId)
    {
        $module = Module::with(['submissions.user', 'submissions.grade', 'submissions.files'])
            ->findOrFail($moduleId);

        return view('assignments.submissions', compact('module'));
    }

    /**
     * Grade a submission
     */
    public function createGrade(Request $request, Module $module)
    {
        $validated = $request->validate([
            'grade'         => 'required|numeric|min:0|max:' . ($module->max_grade ?? 100),
            'comment'       => 'nullable|string|max:2000',
            'submission_id' => 'required|exists:submissions,id',
        ]);

        Grade::updateOrCreate(
            ['submission_id' => $validated['submission_id']],
            [
                'grade'      => $validated['grade'],
                'comment'    => $validated['comment'],
                'teacher_id' => Auth::id(),
            ]
        );

        return redirect()->route('assignments.submissions', $module)
            ->with('success', 'Note attribuée avec succès.');
    }

    /**
     * Compose a test (your original method)
     */
    public function composeTest(Request $request, Module $module)
    {
        $validated = $request->validate([
            'selected_files' => 'required|array',
            'selected_files.*' => 'exists:assignment_files,id',
            'instructions'   => 'required|string',
        ]);

        // Your compose logic here...

        return redirect()->route('assignments.show', $module)
            ->with('success', 'Épreuve composée avec succès.');
    }
}