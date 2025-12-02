<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\AssignmentFile;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
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
        // Just find the module and display the submissions page
        $module = Module::findOrFail($moduleId);
        
        // Return the view with the module data
        return view('assignment-submissions', compact('module'));
    }
}