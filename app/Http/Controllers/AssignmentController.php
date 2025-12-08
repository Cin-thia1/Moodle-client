<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\AssignmentFile;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Show a paginated list of assignment modules.
     */
    public function index(Request $request)
    {
        // If your Module table stores 'modname' = 'assign' for assignments,
        // filter by that. Otherwise remove the where clause to list all modules.
        $assignments = Module::where('modname', 'assign')
                             ->with(['section', 'course']) // if relations exist
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        // return a view (resources/views/assignments/index.blade.php)
        return view('assignments.index', compact('assignments'));
    }

    public function show(Module $module)
    {
        return view('assignments.show', compact('module'));
    }

    public function createGrade(Request $request, Module $module)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
        ]);

        $validated['teacher_id'] = Auth::id();

        Grade::create($validated);

        return redirect()->route('assignments.show', $module)
            ->with('success', 'Note attribuée avec succès.');
    }

    public function composeTest(Request $request, Module $module)
    {
        $validated = $request->validate([
            'selected_files' => 'required|array',
            'selected_files.*' => 'exists:assignment_files,id',
            'instructions' => 'required|string',
        ]);

        // Logique pour composer l'épreuve
        // ...

        return redirect()->route('assignments.show', $module)
            ->with('success', 'Épreuve composée avec succès.');
    }

    public function submissions($moduleId)
    {
        $module = Module::findOrFail($moduleId);

        return view('assignment-submissions', compact('module'));
    }
}
