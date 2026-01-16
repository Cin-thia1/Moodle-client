@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Assignment</h1>
    <x-form :action="route('assignments.update', $assignment)" method="PUT" :value="$assignment" buttonText="Update" :moduleId="$assignment->module_id" />
</div>
@endsection
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Modifier l'évaluation
                    </h1>
                    <a href="{{ url()->previous() }}" 
                       class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire d'édition -->
            <form action="{{ route('assignments.update', $assignment) }}" method="POST" class="p-6 lg:p-8">
                @csrf
                @method('PUT')

                <!-- Nom de l'évaluation -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'évaluation
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $assignment->name) }}" 
                           required 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date limite -->
                <div class="mb-6">
                    <label for="duedate" class="block text-sm font-medium text-gray-700 mb-2">
                        Date limite
                    </label>
                    <input type="datetime-local" 
                           id="duedate" 
                           name="duedate" 
                           value="{{ old('duedate', $assignment->duedate ? $assignment->duedate->format('Y-m-d\TH:i') : '') }}" 
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
                           value="{{ old('attemptnumber', $assignment->attemptnumber ?? 1) }}" 
                           required 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                    @error('attemptnumber')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sélection du module -->
                <div class="mb-6">
                    <label for="module_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Module associé
                    </label>
                    <select id="module_id" 
                            name="module_id" 
                            required 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                        <option value="">— Sélectionner un module —</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}" 
                                    {{ old('module_id', $assignment->module_id) == $module->id ? 'selected' : '' }}>
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
                    <select id="questions" 
                            name="questions[]" 
                            multiple 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm h-40">
                        @foreach($questions as $question)
                            <option value="{{ $question->id }}" 
                                    {{ in_array($question->id, old('questions', $assignment->questions->pluck('id')->toArray())) ? 'selected' : '' }}>
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
                    <button type="button" 
                            onclick="history.back()" 
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                        Annuler
                    </button>

                    <button type="submit" 
                            id="submitBtn"
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md transition flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btnText">Mettre à jour l'évaluation</span>
                        <svg id="spinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour feedback pendant soumission -->
<script>
    document.getElementById('submitBtn').addEventListener('click', function() {
        this.disabled = true;
        document.getElementById('btnText').textContent = 'Mise à jour en cours...';
        document.getElementById('spinner').classList.remove('hidden');
    });
</script>
@endsection
