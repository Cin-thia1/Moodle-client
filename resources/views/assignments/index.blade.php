@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid grid-cols-12 gap-6">

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR                                               --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <aside class="col-span-12 md:col-span-3">
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>
        <div class="space-y-2">
          @foreach($courses as $course)
            <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-md text-sm transition
                 {{ (int)$selectedCourseId === (int)$course->id
                     ? 'bg-blue-50 text-blue-700 font-semibold'
                     : 'hover:bg-gray-50 text-gray-700' }}">
              <span class="truncate">{{ $course->fullname }}</span>
              @if((int)$selectedCourseId === (int)$course->id)
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Actif</span>
              @endif
            </a>
          @endforeach
        </div>
      </div>

      {{-- Boutons ajouter : seulement enseignant --}}
      @if($isTeacher)
        <div class="mt-4 space-y-2">
          <a href="{{ route('assignments.create', ['course_id' => $selectedCourseId]) }}"
             class="w-full block text-center bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition">
            + Ajouter un devoir
          </a>
          <a href="{{ route('quiz.create', ['course_id' => $selectedCourseId]) }}"
             class="w-full block text-center bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700 transition">
            + Ajouter un quiz
          </a>
        </div>
      @endif
    </aside>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- CONTENU : LISTE DEVOIRS + QUIZ                        --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <main class="col-span-12 md:col-span-9">
      <div class="bg-white rounded-lg shadow">

        <div class="p-4 border-b">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h1 class="text-lg font-bold text-gray-800">Devoirs</h1>
              <p class="text-xs text-gray-500">
                @if($isTeacher)
                  Liste des devoirs et quiz du cours sélectionné.
                @else
                  Remets ton travail et consulte ta note.
                @endif
              </p>
            </div>
            <div class="w-64 hidden md:block">
              <input type="text" id="searchAssignments"
                     placeholder="Rechercher un devoir..."
                     class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring">
            </div>
          </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
          <div class="mx-4 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
            {{ session('success') }}
          </div>
        @endif

        <div class="p-4">
          @if($assignments->isEmpty())
            <div class="text-sm text-gray-600 bg-gray-50 rounded-md p-4">
              Aucun devoir pour ce cours.
            </div>
          @else
            @foreach($assignments as $a)
              @php
                $isQuiz = $a->modname === 'quiz';
              @endphp

              <div class="assignment-item px-3 py-3 rounded-md hover:bg-gray-50 transition"
                   data-name="{{ strtolower($a->name) }}">
                <div class="flex items-start justify-between gap-4">

                  {{-- Infos --}}
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                      {{-- Badge type --}}
                      @if($isQuiz)
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-semibold shrink-0">Quiz</span>
                      @else
                        <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-semibold shrink-0">Devoir</span>
                      @endif

                      <a href="{{ $isQuiz ? route('quiz.show', $a->id) : route('assignments.show', $a->id) }}"
                         class="font-semibold text-gray-800 hover:underline truncate">
                        {{ $a->name }}
                      </a>
                    </div>

                    <div class="text-xs text-gray-500 mt-1 ml-0">
                      @if($isQuiz)
                        @if($a->timeclose)
                          Fermeture : <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($a->timeclose)->format('d/m/Y H:i') }}</span> •
                        @endif
                        Note max : <span class="font-medium text-gray-700">{{ $a->grade ?? '—' }}</span>
                        @if($a->timelimit)
                          • ⏱ {{ $a->timelimitFormatted() }}
                        @endif
                      @else
                        Date limite : <span class="font-medium text-gray-700">{{ $a->duedate ? \Carbon\Carbon::parse($a->duedate)->format('d/m/Y H:i') : '—' }}</span>
                        • Barème : <span class="font-medium text-gray-700">{{ $a->grade ?? '—' }}</span>
                      @endif
                    </div>

                    {{-- Badge statut élève (devoir PDF uniquement) --}}
                    @if(!$isTeacher && !$isQuiz)
                      @php $sub = $mySubs[$a->id] ?? null; @endphp
                      <div class="flex flex-wrap gap-2 mt-2">
                        @if(!$sub)
                          <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pas remis</span>
                        @elseif($sub->status === 'submitted')
                          <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Remis</span>
                        @elseif($sub->status === 'graded')
                          <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Noté</span>
                        @else
                          <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $sub->status }}</span>
                        @endif
                        @if($sub && $sub->grade !== null)
                          <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">Note : {{ $sub->grade }}/{{ $a->grade ?? 100 }}</span>
                        @endif
                      </div>
                    @endif
                  </div>

                  {{-- Actions --}}
                  <div class="shrink-0 flex items-center gap-1">
                    <a href="{{ $isQuiz ? route('quiz.show', $a->id) : route('assignments.show', $a->id) }}"
                       class="text-sm font-medium text-blue-600 hover:underline px-2 py-1">
                      Ouvrir
                    </a>

                    @if($isTeacher)
                      <span class="text-gray-300 select-none px-0.5">|</span>

                      {{-- Modifier --}}
                      <a href="{{ $isQuiz ? route('quiz.edit', $a->id) : route('assignments.edit', $a->id) }}"
                         title="Modifier"
                         class="inline-flex items-center gap-1 text-xs font-medium text-amber-600
                                hover:text-amber-800 hover:bg-amber-50 px-2 py-1 rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                   m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="hidden sm:inline">Modifier</span>
                      </a>

                      {{-- Supprimer --}}
                      <form action="{{ $isQuiz ? route('quiz.destroy', $a->id) : route('assignments.destroy', $a->id) }}"
                            method="POST" class="inline"
                            onsubmit="return confirm('Supprimer « {{ addslashes($a->name) }} » ? Action irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Supprimer"
                                class="inline-flex items-center gap-1 text-xs font-medium text-red-500
                                       hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded transition">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                               viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                     m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                          <span class="hidden sm:inline">Supprimer</span>
                        </button>
                      </form>
                    @endif
                  </div>

                </div>
              </div>
            @endforeach
          @endif
        </div>

      </div>
    </main>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchAssignments');
    if (!input) return;
    const items = document.querySelectorAll('.assignment-item');
    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        items.forEach(item => {
            item.style.display = item.dataset.name.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endsection