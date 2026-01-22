@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid grid-cols-12 gap-6">

    {{-- SIDEBAR COURS --}}
    <aside class="col-span-12 md:col-span-3 space-y-4">
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>
        <div class="space-y-2">
          @foreach($courses as $c)
            <a href="{{ route('courses.gradebook', $c->id) }}"
               class="block px-3 py-2 rounded-md text-sm transition
                 {{ (int)$course->id === (int)$c->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
              {{ $c->fullname }}
            </a>
          @endforeach
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
           class="block text-center bg-gray-900 text-white text-sm px-4 py-2 rounded-md hover:bg-black transition">
          ← Retour aux devoirs
        </a>
      </div>
    </aside>

    {{-- CONTENU --}}
    <main class="col-span-12 md:col-span-9">
      <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
          <h1 class="text-lg font-bold text-gray-800">Carnet de notes</h1>
          <p class="text-xs text-gray-500 mt-1">
            Cours : <span class="font-medium text-gray-700">{{ $course->fullname }}</span>
          </p>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Élève</th>
                @foreach($assignments as $a)
                  <th class="text-center px-3 py-3 font-semibold text-gray-600 whitespace-nowrap">
                    {{ $a->name }}
                    <div class="text-[11px] text-gray-400 font-normal">
                      / {{ $a->grade ?? 100 }}
                    </div>
                  </th>
                @endforeach
              </tr>
            </thead>

            <tbody class="divide-y">
              @forelse($students as $student)
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                    {{ $student->name }}
                  </td>

                  @foreach($assignments as $a)
                    @php
                      $g = $matrix[$student->id][$a->id] ?? null;
                    @endphp

                    <td class="text-center px-3 py-3">
                      @if($g !== null)
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">
                          {{ $g }}
                        </span>
                      @else
                        <span class="text-gray-400">—</span>
                      @endif
                    </td>
                  @endforeach
                </tr>
              @empty
                <tr>
                  <td colspan="{{ 1 + $assignments->count() }}" class="px-4 py-6 text-center text-gray-500">
                    Aucun étudiant.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t text-xs text-gray-500">
          Notes récupérées depuis <code>submissions.grade</code> (assignment_id = modules.id).
        </div>
      </div>
    </main>

  </div>
</div>
@endsection
