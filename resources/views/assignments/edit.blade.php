{{--@extends('layouts.app')

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
--}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <div class="bg-white rounded-lg shadow overflow-hidden">

    {{-- Header --}}
    <div class="p-6 border-b bg-gray-50">
      <div class="flex items-center gap-3">
        <a href="{{ route('assignments.show', $module->id) }}"
           class="text-gray-500 hover:text-gray-800 transition text-sm">
          ← Retour
        </a>
        <span class="text-gray-300">|</span>
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Modifier le devoir</h2>
          <p class="text-sm text-gray-600 mt-1">
            Les modifications seront visibles immédiatement par les élèves.
          </p>
        </div>
      </div>
    </div>

    {{-- Contenu --}}
    <div class="p-6">

      {{-- Erreurs --}}
      @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded">
          <ul class="list-disc ml-5 text-sm">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('assignments.update', $module->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Cours (lecture seule - informatif) --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Matière (cours)
          </label>
          <div class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
            {{ $module->section->course->fullname ?? '—' }}
          </div>
          <p class="text-xs text-gray-500 mt-1">
            Le cours ne peut pas être modifié. Pour déplacer le devoir, supprimez-le et recréez-le.
          </p>
        </div>

        {{-- Section --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Section du cours
          </label>
          <select name="section_id" class="w-full border rounded-lg px-3 py-2 text-sm">
            @forelse($sections as $sec)
              <option value="{{ $sec->id }}"
                {{ (int)old('section_id', $module->section_id) === (int)$sec->id ? 'selected' : '' }}>
                {{ $sec->name }}
              </option>
            @empty
              <option value="">Aucune section disponible</option>
            @endforelse
          </select>
        </div>

        {{-- Nom du devoir --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Nom du devoir <span class="text-red-500">*</span>
          </label>
          <input type="text"
                 name="name"
                 value="{{ old('name', $module->name) }}"
                 required
                 class="w-full border rounded-lg px-3 py-2 text-sm"
                 placeholder="Ex : Devoir 1 – Analyse mathématique">
        </div>

        {{-- Instructions --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Instructions
          </label>
          <textarea name="intro"
                    rows="4"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Consignes, règles, format attendu…">{{ old('intro', $module->intro) }}</textarea>
        </div>

        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description (optionnel)
          </label>
          <textarea name="activity"
                    rows="3"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Description complémentaire…">{{ old('activity', $module->activity) }}</textarea>
        </div>

        {{-- Date + barème --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Date limite
            </label>
            <input type="datetime-local"
                   name="duedate"
                   value="{{ old('duedate', $module->duedate ? \Carbon\Carbon::parse($module->duedate)->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-500 mt-1">
              La date sera mise à jour dans le calendrier automatiquement.
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Barème (sur 100) <span class="text-red-500">*</span>
            </label>
            <input type="number"
                   name="grade"
                   min="0"
                   max="100"
                   value="{{ old('grade', $module->grade ?? 100) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
        </div>

        {{-- PDF --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Énoncé du devoir (PDF)
          </label>

          {{-- Afficher le PDF actuel --}}
          @if($module->pdf_url)
            <div class="mb-3 flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
              <div>
                <div class="text-sm font-medium text-blue-800">📄 Fichier actuel</div>
                <div class="text-xs text-blue-600 mt-0.5">{{ $module->pdf_filename }}</div>
              </div>
              <a href="{{ asset($module->pdf_url) }}"
                 target="_blank"
                 class="text-sm text-blue-600 hover:underline">
                Voir
              </a>
            </div>
            <p class="text-xs text-gray-500 mb-2">
              Uploader un nouveau PDF remplacera l'ancien fichier définitivement.
            </p>
          @endif

          <input type="file"
                 name="pdf"
                 accept="application/pdf"
                 class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
          <p class="text-xs text-gray-500 mt-1">PDF uniquement – maximum 10 Mo.</p>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center pt-4 border-t">
          <a href="{{ route('assignments.show', $module->id) }}"
             class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
            Annuler
          </a>

          <button type="submit"
                  class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
            💾 Enregistrer les modifications
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection