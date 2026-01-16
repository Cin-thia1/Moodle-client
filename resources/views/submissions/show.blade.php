@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg">
            <!-- Informations générales de la soumission -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Soumission de {{ $submission->student->name }}</h2>
                <p class="text-sm text-gray-600">Évaluation : {{ $submission->assignment->name }}</p>
                <p class="text-sm text-gray-600">
                    Statut :
                    <span class="px-2 py-1 rounded text-white {{ $submission->status == 'pending' ? 'bg-yellow-500' : 'bg-green-500' }}">
                        {{ $submission->status == 'pending' ? 'En attente de correction' : 'Corrigée' }}
                    </span>
                </p>
                @if($submission->status == 'corrected' && $submission->grade)
                    <p class="text-sm text-gray-800 font-semibold">Note : {{ $submission->grade->grade }}/20</p>
                    <p class="text-sm text-gray-600">Commentaire : {{ $submission->grade->comment }}</p>
                @endif
            </div>

            <!-- Liste des questions et réponses -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Réponses de l'étudiant</h3>
                @if($submissionQuestions->isNotEmpty())
                    <ul class="divide-y divide-gray-200">
                        @foreach($submissionQuestions as $question)
                            <li class="py-4">
                                <div>
                                    <p class="text-gray-800 font-semibold">{{ $loop->iteration }}. {{ $question->content }}</p>
                                    <p class="mt-2 text-gray-600">
                                        Réponse fournie :
                                        <span class="font-bold {{ $question->student_answer_id == $question->correct_choice_id ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $question->choices[$question->student_answer_id] ?? 'Non répondu' }}
                                        </span>
                                    </p>
                                    
                                    <!-- Dropdown pour voir les choix -->
                                    <details class="mt-2">
                                        <summary class="text-blue-500 hover:underline cursor-pointer">Voir les choix</summary>
                                        <ul class="mt-2 space-y-1 bg-gray-100 p-2 rounded-lg">
                                            @foreach($question->choices as $key => $choice)
                                                <li class="{{ $key == $question->correct_choice_id ? 'text-green-600 font-bold' : 'text-gray-700' }}">
                                                    {{ chr(65 + $key) }}. {{ $choice }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">Aucune question disponible pour cette soumission.</p>
                @endif
            </div>

            <!-- Formulaire de notation pour les enseignants -->
            @if(Auth::user()->hasRole('ROLE_TEACHER') && $submission->status == 'pending')
                <div class="p-6 border-t border-gray-200">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Attribuer une note</h4>
                    <form action="{{ route('submissions.grade', $submission->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="grade" class="block text-sm font-medium text-gray-700">Note (/20)</label>
                            <input type="number" class="form-input mt-1 block w-full" name="grade" id="grade" min="0" max="20" required>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="block text-sm font-medium text-gray-700">Commentaire</label>
                            <textarea class="form-textarea mt-1 block w-full" name="comment" id="comment"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Soumettre la note
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
