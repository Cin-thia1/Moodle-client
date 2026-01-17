@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <div class="bg-white rounded-lg shadow overflow-hidden">

    {{-- Header --}}
    <div class="p-6 border-b bg-gray-50">
      <h2 class="text-2xl font-bold text-gray-800">
        Ajouter un devoir
      </h2>
      <p class="text-sm text-gray-600 mt-1">
        Créer une évaluation avec énoncé PDF, date limite et barème.
      </p>
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

      <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Cours --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Matière (cours)
          </label>
          <select name="course_id"
                  class="w-full border rounded-lg px-3 py-2 text-sm"
                  onchange="window.location='{{ route('assignments.create') }}?course_id='+this.value">
            @foreach($courses as $course)
              <option value="{{ $course->id }}"
                {{ (int)old('course_id', $selectedCourseId) === (int)$course->id ? 'selected' : '' }}>
                {{ $course->fullname }}
              </option>
            @endforeach
          </select>
          <p class="text-xs text-gray-500 mt-1">
            Le changement recharge la page pour afficher les sections du cours.
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
                {{ (int)old('section_id') === (int)$sec->id ? 'selected' : '' }}>
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
            Nom du devoir
          </label>
          <input type="text"
                 name="name"
                 value="{{ old('name') }}"
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
                    placeholder="Consignes, règles, format attendu…">{{ old('intro') }}</textarea>
        </div>

        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description (optionnel)
          </label>
          <textarea name="activity"
                    rows="3"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Description complémentaire…">{{ old('activity') }}</textarea>
        </div>

        {{-- Date + barème --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Date limite
            </label>
            <input type="datetime-local"
                   name="duedate"
                   value="{{ old('duedate') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Barème (sur 100)
            </label>
            <input type="number"
                   name="grade"
                   min="0"
                   max="100"
                   value="{{ old('grade', 100) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
        </div>

        {{-- PDF --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Énoncé du devoir (PDF)
          </label>
          <input type="file"
                 name="pdf"
                 accept="application/pdf"
                 class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
          <p class="text-xs text-gray-500 mt-1">
            PDF uniquement – maximum 10 Mo.
          </p>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-2 pt-4">
          <a href="{{ route('assignments.index', ['course_id' => $selectedCourseId]) }}"
             class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
            Annuler
          </a>

          <button type="submit"
                  class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
            Créer le devoir
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection
