<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['submission', 'teacher'])->get();
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        $submissions = Submission::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('grades.create', compact('submissions', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        Grade::create($validated);
        return redirect()->route('grades.index')->with('success', 'Grade ajouté avec succès.');
    }

    public function show(Grade $grade)
    {
        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        $submissions = Submission::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('grades.edit', compact('grade', 'submissions', 'teachers'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $grade->update($validated);
        return redirect()->route('grades.index')->with('success', 'Grade mis à jour avec succès.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('grades.index')->with('success', 'Grade supprimé avec succès.');
    }
}
