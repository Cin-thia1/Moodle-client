@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Ajouter une Nouvelle Question</h3>

                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('questions.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenu de la question</label>
                            <textarea id="content" name="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="choices" class="block text-sm font-medium text-gray-700">Propositions</label>
                            <input type="text" id="choices" name="choices[]" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('choices.0') }}" required />
                            <input type="text" id="choices" name="choices[]" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('choices.1') }}" required />
                            <input type="text" id="choices" name="choices[]" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('choices.2') }}" />
                            <input type="text" id="choices" name="choices[]" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('choices.3') }}" />
                        </div>

                        <div class="mb-4">
                            <label for="correct_choice_id" class="block text-sm font-medium text-gray-700">Choix Correct</label>
                            <select id="correct_choice_id" name="correct_choice_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                <option value="1" {{ old('correct_choice_id') == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('correct_choice_id') == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('correct_choice_id') == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('correct_choice_id') == 4 ? 'selected' : '' }}>4</option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                                Ajouter la Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
