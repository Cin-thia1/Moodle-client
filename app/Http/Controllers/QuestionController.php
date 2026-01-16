<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Afficher la liste des questions
    public function index()
    {
        $questions = Question::all();
        return view('questions.index', compact('questions'));
    }

    // Afficher le formulaire pour créer une nouvelle question
    public function create()
    {
        return view('questions.create');
    }

    // Enregistrer une nouvelle question
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'choices' => 'required|array|min:2',
            'correct_choice_id' => 'required|integer',
        ]);

        Question::create([
            'content' => $request->content,
            'choices' => $request->choices,
            'correct_choice_id' => $request->correct_choice_id,
        ]);

        return redirect()->back()
                         ->with('success', 'Question créée avec succès.');
    }

    // Afficher le formulaire pour modifier une question
    public function edit($questionId)
    {
        $question = Question::findOrFail($questionId);
        return view('questions.edit', compact('question'));
    }

    // Mettre à jour une question
    public function update(Request $request, $questionId)
    {
        $request->validate([
            'content' => 'required|string',
            'choices' => 'required|array|min:2',
            'correct_choice_id' => 'required|integer',
        ]);

        $question = Question::findOrFail($questionId);

        $question->update([
            'content' => $request->content,
            'choices' => $request->choices,
            'correct_choice_id' => $request->correct_choice_id,
        ]);

        return redirect()->route('questions.index')
                         ->with('success', 'Question mise à jour avec succès.');
    }

    // Supprimer une question
    public function destroy($questionId)
    {
        $question = Question::findOrFail($questionId);

        $question->delete();

        return redirect()->back()
                         ->with('success', 'Question supprimée avec succès.');
    }
}
