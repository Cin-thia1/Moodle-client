@extends('layouts.app')

@section('content')
<<<<<<< HEAD
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
=======
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Soumissions - {{ $module->name ?? 'Évaluation' }}
            </h1>
            <p class="mt-2 text-gray-600">
                Liste des devoirs déposés par les étudiants
            </p>
        </div>

        <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>

    <!-- Submissions List -->
    @if($submissions->isEmpty())
        <div class="text-center py-16 bg-gray-50 rounded-xl border border-gray-200">
            <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-700 mb-2">Aucune soumission disponible</h3>
            <p class="text-gray-500">Les étudiants n'ont pas encore déposé leurs devoirs pour cette évaluation.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <!-- Summary Stats (optional) -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Total soumissions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $submissions->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{ $submissions->where('status', 'submitted')->count() }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Corrigées</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $submissions->where('grade')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Étudiant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date de soumission</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($submissions as $submission)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">
                                    {{ $submission->user->name ?? 'Étudiant inconnu' }}
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-700">
                                    {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-6 py-5 text-sm">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ $submission->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 
                                           $submission->status === 'graded' || $submission->status === 'corrected' ? 'bg-green-100 text-green-800' : 
                                           $submission->status === 'late' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">
                                    @if($submission->grade)
                                        {{ $submission->grade->grade }} / {{ $submission->module->max_grade ?? 20 }}
                                    @else
                                        <span class="text-gray-500">Non corrigée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-sm font-medium">
                                    <a href="{{ route('submissions.show', $submission) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                    @if(!$submission->grade && auth()->user()->hasRole('ROLE_TEACHER'))
                                        <a href="#" 
                                           onclick="event.preventDefault(); document.getElementById('grade-form-{{ $submission->id }}').classList.toggle('hidden');"
                                           class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-edit mr-1"></i> Noter
                                        </a>
                                    @endif
                                </td>
                            </tr>

                            <!-- Inline grading form (teacher only) -->
                            @if(auth()->user()->hasRole('ROLE_TEACHER') && !$submission->grade)
                                <tr id="grade-form-{{ $submission->id }}" class="hidden bg-gray-50">
                                    <td colspan="6" class="px-6 py-4">
                                        <form action="{{ route('assignments.createGrade', $submission->module) }}" method="POST" class="flex flex-wrap gap-4 items-end">
                                            @csrf
                                            <input type="hidden" name="submission_id" value="{{ $submission->id }}">

                                            <div>
                                                <label class="block text-sm text-gray-700 mb-1">Note</label>
                                                <input type="number" name="grade" min="0" max="{{ $submission->module->max_grade ?? 20 }}" 
                                                       class="border border-gray-300 rounded-lg px-3 py-2 w-24" required>
                                            </div>

                                            <div class="flex-1">
                                                <label class="block text-sm text-gray-700 mb-1">Commentaire</label>
                                                <textarea name="comment" rows="2" 
                                                          class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                                            </div>

                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
                                                Enregistrer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
>>>>>>> Evaluation
