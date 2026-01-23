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
          <div class="flex items-start justify-between gap-3">
            <div>
              <h1 class="text-lg font-bold text-gray-800">Carnet de notes</h1>
              <p class="text-xs text-gray-500 mt-1">
                Cours : <span class="font-medium text-gray-700">{{ $course->fullname }}</span>
              </p>
            </div>

            {{-- ✅ Un seul bouton enregistrer --}}
            <button form="gradebookForm"
                    type="submit"
                    class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition shrink-0">
              Enregistrer
            </button>
          </div>

          @if(session('success'))
            <div class="mt-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
              {{ session('success') }}
            </div>
          @endif

          @if ($errors->any())
            <div class="mt-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
              <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>

        <form id="gradebookForm" method="POST" action="{{ route('gradebook.save', $course->id) }}">
          @csrf
          @method('PATCH')

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
                        $allowed = $canGrade[$student->id][$a->id] ?? false;
                      @endphp

                      <td class="text-center px-3 py-3">
                        <input type="number"
                               name="grades[{{ $student->id }}][{{ $a->id }}]"
                               min="0"
                               max="{{ $a->grade ?? 100 }}"
                               value="{{ $g ?? '' }}"
                               placeholder="—"
                               class="w-20 border rounded-md px-2 py-1 text-sm text-center
                                      {{ $allowed ? 'bg-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                               {{ $allowed ? '' : 'disabled' }}>
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
        </form>

        <div class="p-4 border-t text-xs text-gray-500">
          Les notes sont enregistrées dans <code>submissions.grade</code>
          (liaison : <code>submissions.module_id = modules.id</code>).
        </div>

      </div>
    </main>

  </div>
</div>
@endsection
