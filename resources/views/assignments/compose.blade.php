@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg">
            <!-- Titre de l'évaluation -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">{{ $assignment->name }}</h2>
                <p class="text-sm text-gray-600">Date limite : {{ \Carbon\Carbon::parse($assignment->duedate)->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Bouton Retour -->
            <div class="p-6">
                <a href="{{ url()->previous() }}" 
                   class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>

            <!-- Formulaire de soumission -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Questions</h3>
                <form action="{{ route('assignments.submit', $assignment->id) }}" method="POST">
                    @csrf
                    
                    @if($submissionQuestions && count($submissionQuestions) > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach ($submissionQuestions as $question)
                                <li class="py-4">
                                    <div>
                                        <p class="text-gray-800">{{ $loop->iteration }}. {{ $question->content }}</p>
                                        <ul class="mt-2 space-y-1">
                                            @foreach($question->choices as $key => $choice)
                                                <li>
                                                    <div class="flex items-center">
                                                        <input type="radio" id="question_{{ $question->id }}_choice_{{ $key }}" 
                                                               name="answers[{{ $question->id }}]" 
                                                               value="{{ $key }}" 
                                                               class="form-radio">
                                                        <label for="question_{{ $question->id }}_choice_{{ $key }}" 
                                                               class="ml-2 text-gray-600">
                                                            {{ chr(65 + $key) }}. {{ $choice }}
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-600">Aucune question disponible pour cette soumission.</p>
                    @endif

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Soumettre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
