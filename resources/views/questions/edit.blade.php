@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Modifier la Question</h3>

                    <!-- Formulaire de modification -->
                    <form action="{{ route('questions.update', $question->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenu de la question</label>
                            <textarea id="content" name="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('content', $question->content) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="choices" class="block text-sm font-medium text-gray-700">Propositions</label>
                            @foreach(is_string($question->choices) ? json_decode($question->choices) : $question->choices as $index => $choice)
                                <input type="text" id="choices" name="choices[]" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('choices.' . $index, $choice) }}" required />
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label for="correct_choice_id" class="block text-sm font-medium text-gray-700">Choix Correct</label>
                            <select id="correct_choice_id" name="correct_choice_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                @foreach(is_string($question->choices) ? json_decode($question->choices) : $question->choices as $index => $choice)
                                    <option value="{{ $index }}" {{ old('correct_choice_id', $question->correct_choice_id) == $index ? 'selected' : '' }}>
                                        {{ chr(65 + $index) }}. {{ $choice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                                Mettre à Jour la Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
