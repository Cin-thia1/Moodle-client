<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\AssignmentFile;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->query('view', 'assignments');
    
        $assignments = collect(); // default empty
        $submissions = collect(); // default empty
    
        if ($view === 'assignments') {
            $assignments = Module::where('modname', 'assign')
                ->with(['section', 'course'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($view === 'submissions') {
            // Load submissions (adapt to your real query)
            $submissions = Submission::with(['user', 'assignment', 'grade'])
                ->latest()
                ->paginate(15);
            // OR filter by module if needed
            // $submissions = Submission::whereHas('assignment.module_id', ...)->...
        }
    
        return view('assignments.index', compact('assignments', 'submissions'));
    }
  /**
 * Show the form for creating a new assignment (UC-T1)
 */
public function create()
{
    $modules = \App\Models\Module::orderBy('name')->get();

    // Temporarily disable questions loading until the table exists
    // $questions = \App\Models\Question::orderBy('content')->get();
    $questions = collect(); // empty collection → select will be empty but no crash

    return view('assignments.create', compact('modules', 'questions'));
}
    /**
     * Teacher - Store a newly created assignment (UC-T1)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'due_date'          => 'nullable|date|after:now',
            'max_grade'         => 'required|integer|min:1|max:100',
            'file'              => 'nullable|file|mimes:pdf,doc,docx,txt,zip,jpg,png|max:10240', // 10MB
            'type'              => 'required|in:pdf,quiz,qcm,text,other',
        ]);

        $data = [
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'due_date'    => $validated['due_date'],
            'max_grade'   => $validated['max_grade'],
            'modname'     => 'assign',
            'type'        => $validated['type'],
            'created_by'  => Auth::id(),
        ];

        // Handle file upload
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
     * Both roles - Show details of one assignment (UC-S2 / UC-T4 preparation)
     */
    public function show(Module $module)
    {
        // Make sure it's an assignment
        if ($module->modname !== 'assign') {
            abort(404);
        }

        $module->load([
            'submissions' => function ($q) {
                $q->with(['user', 'grade', 'files']);
            }
        ]);

        return view('assignments.show', compact('module'));
    }

    /**
     * Teacher - List all submissions for one assignment (UC-T4)
     */
    public function submissions(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        $module->load([
            'submissions' => function ($q) {
                $q->with(['user', 'grade', 'files'])->latest();
            }
        ]);

        return view('assignments.submissions', compact('module'));
    }

    /**
     * Student - Submit work for an assignment (UC-S4 - offline prepared version would store locally first)
     */
    public function submit(Request $request, Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        // You can add check if due date passed, etc.
        if ($module->due_date && now()->gt($module->due_date)) {
            return back()->withErrors(['late' => 'La date limite est dépassée.']);
        }

        $validated = $request->validate([
            'submission_file'   => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,png,zip|max:20480',
            'comment'           => 'nullable|string|max:2000',
        ]);

        $submission = Submission::create([
            'module_id'     => $module->id,
            'user_id'       => Auth::id(),
            'status'        => 'submitted',
            'comment'       => $validated['comment'] ?? null,
            'submitted_at'  => now(),
            'attempt_number' => 1, // you can increment later
        ]);

        if ($request->hasFile('submission_file')) {
            $path = $request->file('submission_file')->store('submissions/' . $module->id, 'public');
            AssignmentFile::create([
                'submission_id' => $submission->id,
                'file_path'     => $path,
                'original_name' => $request->file('submission_file')->getClientOriginalName(),
            ]);
        }

        return redirect()
            ->route('assignments.show', $module)
            ->with('success', 'Votre devoir a été déposé avec succès.');
    }

    /**
     * Teacher - Grade a submission + publish note (UC-T5)
     */
    public function createGrade(Request $request, Module $module)
    {
        $validated = $request->validate([
            'grade'         => 'required|numeric|min:0|max:' . $module->max_grade,
            'comment'       => 'nullable|string|max:2000',
            'submission_id' => 'required|exists:submissions,id',
        ]);

        $grade = Grade::updateOrCreate(
            ['submission_id' => $validated['submission_id']],
            [
                'grade'      => $validated['grade'],
                'comment'    => $validated['comment'],
                'teacher_id' => Auth::id(),
                'published_at' => now(),
            ]
        );

        // Here you would add Moodle API sync logic when online
        // $this->syncGradeToMoodle($grade);

        return redirect()
            ->route('assignments.submissions', $module)
            ->with('success', 'Note attribuée et publiée.');
    }

    /**
     * Teacher - Edit existing assignment (UC-T2)
     */
    public function edit(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        return view('assignments.edit', compact('module'));
    }

    /**
     * Teacher - Update assignment (UC-T2)
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
            'file'        => 'nullable|file|mimes:pdf,doc,docx,txt,zip,jpg,png|max:10240',
        ]);

        $data = $validated;

        if ($request->hasFile('file')) {
            // Delete old file if exists
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
     * Teacher - Delete assignment (UC-T3)
     */
    public function destroy(Module $module)
    {
        if ($module->modname !== 'assign') {
            abort(404);
        }

        // Optional: soft delete or check if has submissions
        if ($module->submissions()->exists()) {
            return back()->withErrors(['cannot_delete' => 'Des soumissions existent déjà.']);
        }

        // Delete attached file
        if ($module->file_path) {
            Storage::disk('public')->delete($module->file_path);
        }

        $module->delete();

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Énoncé supprimé.');
    }

    // ----------------------------------------------------------------
    //  Future / Offline related methods (to be expanded later)
    // ----------------------------------------------------------------

    /**
     * Student - List my visible assignments (UC-S1)
     */
    public function studentIndex()
    {
        // This would typically filter by enrolled courses
        $assignments = Module::where('modname', 'assign')
            ->whereHas('course.enrollments', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->with('course')
            ->orderBy('due_date')
            ->get();

        return view('student.assignments', compact('assignments'));
    }
}