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

            <!-- Formulaire pour modifier les questions -->
            <form action="{{ route('assignments.questions.update', $assignment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Modifier les Questions</h3>
                    
                    @foreach($questions as $index => $question)
                        <div class="mb-4">
                            <!-- Champ caché pour l'ID de la question -->
                            <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">

                            <label for="content_{{ $question->id }}" class="block text-sm font-medium text-gray-700">Contenu de la question {{ $loop->iteration }}</label>
                            <textarea id="content_{{ $question->id }}" name="questions[{{ $index }}][content]" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('questions.' . $index . '.content', $question->content) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="choices_{{ $question->id }}" class="block text-sm font-medium text-gray-700">Propositions</label>
                            @foreach(is_string($question->choices) ? json_decode($question->choices) : $question->choices as $key => $choice)
                                <input type="text" name="questions[{{ $index }}][choices][]" value="{{ old('questions.' . $index . '.choices.' . $key, $choice) }}" class="mt-1 block w-full border-gray-300 rounded-md" required />
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label for="correct_choice_id_{{ $question->id }}" class="block text-sm font-medium text-gray-700">Choix Correct</label>
                            <select id="correct_choice_id_{{ $question->id }}" name="questions[{{ $index }}][correct_choice_id]" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                @foreach(is_string($question->choices) ? json_decode($question->choices) : $question->choices as $key => $choice)
                                    <option value="{{ $key }}" {{ old('questions.' . $index . '.correct_choice_id', $question->correct_choice_id) == $key ? 'selected' : '' }}>
                                        {{ chr(65 + $key) }}. {{ $choice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <!-- Bouton pour enregistrer les modifications -->
                <div class="p-6 flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                        Sauvegarder les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
