@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid grid-cols-12 gap-6">

    {{-- SIDEBAR : COURS --}}
    <aside class="col-span-12 md:col-span-3">
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>

        <div class="space-y-2">
          @foreach($courses as $course)
            <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-md text-sm transition
                 {{ (int)$selectedCourseId === (int)$course->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
              <span class="truncate">{{ $course->fullname }}</span>
              @if((int)$selectedCourseId === (int)$course->id)
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Actif</span>
              @endif
            </a>
          @endforeach
        </div>
      </div>

      {{-- Bouton ajouter : seulement enseignant --}}
      @if(auth()->user()->hasRole('ROLE_TEACHER'))
        <div class="mt-4 bg-white rounded-lg shadow p-4">
          <a href="{{ route('assignments.create', ['course_id' => $selectedCourseId]) }}"
             class="w-full block text-center bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition">
            + Ajouter un devoir
          </a>
        </div>
      @endif
    </aside>

    {{-- CONTENU : LISTE DEVOIRS --}}
    <main class="col-span-12 md:col-span-9">
      <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h1 class="text-lg font-bold text-gray-800">Devoirs</h1>
              <p class="text-xs text-gray-500">
                @if(auth()->user()->hasRole('ROLE_TEACHER'))
                  Liste des devoirs du cours sélectionné.
                @else
                  Remets ton travail et consulte ta note.
                @endif
              </p>
            </div>

            <div class="w-64 hidden md:block">
  <input type="text"
         id="searchAssignments"
         placeholder="Rechercher un devoir..."
         class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring">
</div>

          </div>
        </div>

        <div class="p-4">
          @if($assignments->isEmpty())
            <div class="text-sm text-gray-600 bg-gray-50 rounded-md p-4">
              Aucun devoir pour ce cours.
            </div>
          @else
@foreach($assignments as $a)
  <div class="assignment-item px-3 py-3 rounded-md hover:bg-gray-50 transition"
       data-name="{{ strtolower($a->name) }}">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <a href="{{ route('assignments.show', $a->id) }}"
           class="font-semibold text-gray-800 hover:underline block truncate">
          {{ $a->name }}
        </a>
                      <div class="text-xs text-gray-500 mt-1">
                        Date limite :
                        <span class="font-medium text-gray-700">
                          {{ $a->duedate ? \Carbon\Carbon::parse($a->duedate)->format('d/m/Y H:i') : '—' }}
                        </span>
                        • Barème : <span class="font-medium text-gray-700">{{ $a->grade ?? '—' }}</span>
                      </div>

                      {{-- ✅ Élève : statut + note (branché sur submissions) --}}
@if(!auth()->user()->hasRole('ROLE_TEACHER'))
  @php
    // $a->id = module_id
    $sub = $mySubs[$a->id] ?? null;
  @endphp

  <div class="flex flex-wrap gap-2 mt-2">
    {{-- STATUT --}}
    @if(!$sub)
      <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
        Pas remis
      </span>
    @elseif($sub->status === 'submitted')
      <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
        Remis
      </span>
    @elseif($sub->status === 'graded')
      <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
        Noté
      </span>
    @else
      <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
        {{ $sub->status }}
      </span>
    @endif

    {{-- NOTE --}}
    @if($sub && $sub->grade !== null)
      <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
        Note : {{ $sub->grade }}/{{ $a->grade ?? 100 }}
      </span>
    @else
      <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
        Note : —
      </span>
    @endif
  </div>
@endif

                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                      <a href="{{ route('assignments.show', $a->id) }}"
                         class="text-sm font-medium text-blue-600 hover:underline">
                        Ouvrir
                      </a>

                     <!-- @if(!auth()->user()->hasRole('ROLE_TEACHER'))
                        <a href="{{ route('assignments.show', $a->id) }}"
                           class="bg-blue-600 text-white text-xs px-3 py-2 rounded-md hover:bg-blue-700 transition">
                          Remettre
                        </a>
                      @endif-->
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
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
            const name = item.dataset.name;

            if (name.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

@endsection
