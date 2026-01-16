@extends('layouts.app')

@section('content')
<<<<<<< HEAD
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Créer une nouvelle évaluation</h2>
            </div>

            <form action="{{ route('assignments.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'évaluation</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="duedate" class="block text-sm font-medium text-gray-700">Date limite</label>
                        <input type="datetime-local" id="duedate" name="duedate" class="mt-1 block w-full" required>
                    </div>


                    <div class="mb-4">
                        <label for="attemptnumber" class="block text-sm font-medium text-gray-700">Nombre de tentatives</label>
                        <input type="number" id="attemptnumber" name="attemptnumber" min="1" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="module_id" class="block text-sm font-medium text-gray-700">Module</label>
                        <select id="module_id" name="module_id" class="mt-1 block w-full" required>
                            <option value="">Sélectionner un module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sélectionner des questions existantes -->
                    <div class="mb-4">
                        <label for="questions" class="block text-sm font-medium text-gray-700">Questions existantes</label>
                        <select id="questions" name="questions[]" multiple class="mt-1 block w-full">
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}">{{ $question->content }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white hover:bg-blue-700 px-4 py-2 rounded">
                            Créer l'évaluation
                        </button>
                    </div>
=======
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Créer une nouvelle évaluation
                    </h2>
                    <a href="{{ url()->previous() }}"
                       class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="p-6 lg:p-8">
                @csrf

                <!-- Nom de l'évaluation -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'évaluation
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type d'évaluation -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type d'évaluation
                    </label>
                    <select id="type" name="type" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                        <option value="">— Choisir le type —</option>
                        <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF / Document</option>
                        <option value="mcq" {{ old('type') == 'mcq' ? 'selected' : '' }}>QCM (Questions à choix multiple)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload PDF / Document (visible only if type = pdf) -->
                <div class="mb-6" id="pdf-upload-group" style="display: none;">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Fichier PDF / Document <span class="text-red-600">*</span>
                    </label>
                    <input type="file"
                           id="file"
                           name="file"
                           accept=".pdf,.doc,.docx"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Formats acceptés : PDF, Word (max 5 Mo)</p>
                </div>

                <!-- Date limite -->
                <div class="mb-6">
                    <label for="duedate" class="block text-sm font-medium text-gray-700 mb-2">
                        Date limite
                    </label>
                    <input type="datetime-local"
                           id="duedate"
                           name="duedate"
                           value="{{ old('duedate') }}"
                           required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                    @error('duedate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre de tentatives -->
                <div class="mb-6">
                    <label for="attemptnumber" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de tentatives autorisées
                    </label>
                    <input type="number"
                           id="attemptnumber"
                           name="attemptnumber"
                           min="1"
                           value="{{ old('attemptnumber', 1) }}"
                           required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                    @error('attemptnumber')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Module -->
                <div class="mb-6">
                    <label for="module_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Module associé
                    </label>
                    <select id="module_id" name="module_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                        <option value="">— Sélectionner un module —</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                {{ $module->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('module_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Questions existantes (multi-sélection) -->
                <div class="mb-8">
                    <label for="questions" class="block text-sm font-medium text-gray-700 mb-2">
                        Questions à inclure (sélection multiple possible)
                    </label>
                    <select id="questions" name="questions[]" multiple
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm h-40">
                        @foreach($questions as $question)
                            <option value="{{ $question->id }}" {{ in_array($question->id, old('questions', [])) ? 'selected' : '' }}>
                                {{ Str::limit($question->content, 80) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs questions.
                    </p>
                    @error('questions.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="history.back()"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                        Annuler
                    </button>

                    <button type="submit" id="submitBtn"
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md transition flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btnText">Créer l'évaluation</span>
                        <svg id="spinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
>>>>>>> Evaluation
                </div>
            </form>
        </div>
    </div>
</div>
<<<<<<< HEAD
@endsection
=======

<!-- JavaScript pour afficher/masquer l'upload PDF selon le type -->
<script>
    const typeSelect = document.getElementById('type');
    const pdfGroup = document.getElementById('pdf-upload-group');
    const pdfInput = document.getElementById('file');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'pdf') {
            pdfGroup.style.display = 'block';
            pdfInput.required = true;
        } else {
            pdfGroup.style.display = 'none';
            pdfInput.required = false;
            pdfInput.value = ''; // reset file input
        }
    });

    // Trigger on page load if old value exists
    if (typeSelect.value === 'pdf') {
        pdfGroup.style.display = 'block';
        pdfInput.required = true;
    }

    // Disable button during submit + show spinner
    document.getElementById('submitBtn').addEventListener('click', function() {
        this.disabled = true;
        document.getElementById('btnText').textContent = 'Création en cours...';
        document.getElementById('spinner').classList.remove('hidden');
    });
</script>
@endsection
>>>>>>> Evaluation
