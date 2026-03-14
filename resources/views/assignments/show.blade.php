@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid grid-cols-12 gap-6">

    @php
  $selectedCourseId = $module->section->course->id;
@endphp

{{-- SIDEBAR --}}
<aside class="col-span-12 md:col-span-3 space-y-4">

  {{-- COURS --}}
  <div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>

    <div class="space-y-2">
      @forelse($courses as $course)
        <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
           class="flex items-center justify-between px-3 py-2 rounded-md text-sm transition
             {{ (int)$selectedCourseId === (int)$course->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">

          <span class="truncate">{{ $course->fullname }}</span>

          @if((int)$selectedCourseId === (int)$course->id)
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Actif</span>
          @endif
        </a>
      @empty
        <div class="text-sm text-gray-500">Aucun cours.</div>
      @endforelse
    </div>
  </div>

  {{-- DEVOIRS --}}
  <div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-sm font-semibold text-gray-800 mb-3">Devoirs</h2>

    <div class="space-y-2">
      @forelse($assignments as $a)
        <a href="{{ route('assignments.show', $a->id) }}"
           class="block px-3 py-2 rounded-md text-sm transition
             {{ (int)$module->id === (int)$a->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
          {{ $a->name }}
        </a>
      @empty
        <div class="text-sm text-gray-500">Aucun devoir.</div>
      @endforelse
    </div>
  </div>

</aside>


    {{-- CONTENU --}}
    <main class="col-span-12 md:col-span-9">

      {{-- flash messages --}}
      @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          <ul class="list-disc ml-5 text-sm">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- HEADER DEVOIR --}}
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-800 truncate">{{ $module->name }}</h1>

            @if(!empty($module->intro))
              <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $module->intro }}</p>
            @else
              <p class="text-sm text-gray-500 mt-1">Aucune description.</p>
            @endif

            <div class="flex flex-wrap gap-2 mt-3">
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                À rendre :
                {{ $module->duedate ? \Carbon\Carbon::parse($module->duedate)->format('d/m/Y H:i') : 'Non définie' }}
              </span>

              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                Barème : {{ $module->grade ?? 100 }}
              </span>
            </div>

            {{-- PDF --}}
            @if(!empty($module->pdf_url))
              <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between gap-4">
                  <div>
                    <div class="text-sm font-semibold text-gray-800">Énoncé du devoir</div>
                    <div class="text-xs text-gray-500 mt-1">
                      Télécharge le fichier fourni par l’enseignant.
                    </div>
                  </div>

                  <a href="{{ asset($module->pdf_url) }}"
                     target="_blank"
                     class="bg-white border px-3 py-2 rounded-md text-sm hover:bg-gray-100 transition">
                    📄 Voir / Télécharger
                  </a>
                </div>
              </div>
            @else
              <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <div class="text-sm font-semibold text-gray-800">Énoncé du devoir</div>
                <div class="text-xs text-gray-500 mt-1">Aucun fichier d’énoncé attaché.</div>
              </div>
            @endif
          </div>

          @if(auth()->user()?->hasRole('ROLE_TEACHER'))
            <a href="{{ route('courses.gradebook', $module->section->course->id) }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition shrink-0">
              Afficher carnet de notes
            </a>
          @endif
        </div>
      </div>

      {{-- ========================= --}}
      {{-- ======= ENSEIGNANT ====== --}}
      {{-- ========================= --}}
      @if(auth()->user()?->hasRole('ROLE_TEACHER'))
        <div class="bg-white rounded-lg shadow mt-6">
          <div class="p-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Travaux remis</h2>
            <p class="text-xs text-gray-500 mt-1">Suivi des soumissions des étudiants.</p>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Élève</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Email</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Statut</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Fichier</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Commentaire</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Soumis le</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Note</th>
                </tr>
              </thead>

              <tbody class="divide-y">
                @forelse($students as $student)
                  @php
                    $sub = $subByUser[$student->id] ?? null;
                    $status = $sub?->status ?? null;
                  @endphp

                  <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $student->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $student->email }}</td>

                    <td class="px-4 py-3">
                      @if($sub && ($status === 'submitted' || $status === 'graded'))
                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">Remis</span>
                      @elseif($sub)
                        <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700">Brouillon</span>
                      @else
                        <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">Pas remis</span>
                      @endif
                    </td>

                    <td class="px-4 py-3">
                      @if($sub?->file)
                        <a class="text-blue-600 hover:underline" target="_blank" href="{{ asset($sub->file) }}">
                          Voir fichier
                        </a>
                      @else
                        <span class="text-gray-400">—</span>
                      @endif
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                      {{ $sub?->content ?? '—' }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                      {{ $sub?->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') : '—' }}
                    </td>

                    <td class="px-4 py-3">
                      @if($sub)
                        <form method="POST"
                              action="{{ route('assignments.grade', ['module' => $module->id, 'student' => $student->id]) }}"
                              class="flex items-center gap-2">
                          @csrf
                          @method('PATCH')

                          <input type="number"
                                 name="grade"
                                 min="0"
                                 max="{{ $module->grade ?? 100 }}"
                                 value="{{ $sub->grade ?? '' }}"
                                 class="w-24 border rounded-md px-2 py-1 text-sm">

                          <button type="submit"
                                  class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-blue-700 transition">
                            Enregistrer
                          </button>
                        </form>
                      @else
                        <div class="text-xs text-gray-400">Pas de soumission</div>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                      Aucun étudiant inscrit.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        
        </div>

      @else
      {{-- ========================= --}}
      {{-- ========= ELEVE ========= --}}
      {{-- ========================= --}}

        @php
          $myStatus = $mySubmission?->status ?? 'not_submitted';
          $myFile = $mySubmission?->file_path;
          $mySubmittedAt = $mySubmission?->submitted_at;
          $myGrade = $mySubmission?->grade;
        @endphp

        <div class="bg-white rounded-lg shadow mt-6">
          <div class="p-4 border-b">
            <h2 class="text-lg font-bold text-gray-800">Statut de remise</h2>
          </div>

          <div class="p-4">
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm border">
                <tbody class="divide-y">
                  <tr class="bg-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-700 w-64">Statut des travaux remis</td>
                    <td class="px-4 py-3">
                      @if($myStatus === 'submitted' || $myStatus === 'graded')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Remis pour évaluation</span>
                      @else
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Pas remis</span>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-gray-700">Statut de l’évaluation</td>
                    <td class="px-4 py-3">
                      @if($myGrade !== null)
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Noté</span>
                      @else
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Non évalué</span>
                      @endif
                    </td>
                  </tr>

                  <tr class="bg-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-700">Remise de fichiers</td>
                    <td class="px-4 py-3">
                      @if($myFile)
                        <div class="flex items-center justify-between gap-4">
                          <a class="text-blue-600 hover:underline" target="_blank" href="{{ asset($myFile) }}">
                            Voir mon fichier
                          </a>
                          <span class="text-xs text-gray-500">
                            {{ $mySubmittedAt ? \Carbon\Carbon::parse($mySubmittedAt)->format('d/m/Y H:i') : '' }}
                          </span>
                        </div>
                      @else
                        <span class="text-gray-400">—</span>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-gray-700">Note</td>
                    <td class="px-4 py-3">
                      @if($myGrade !== null)
                        <span class="font-semibold text-gray-900">{{ $myGrade }}/{{ $module->grade ?? 100 }}</span>
                      @else
                        <span class="text-gray-500">—</span>
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            {{-- UI remise élève (non branché ici) --}}
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-gray-800">Remettre mon travail</h3>
                <span class="text-xs text-gray-500">(PDF)</span>
              </div>

        <form action="{{ route('assignments.submit', $module->id) }}"
      method="POST"
      enctype="multipart/form-data">

                @csrf
                <div>
                  <label class="block text-sm text-gray-700 mb-1">Fichier</label>
                  <input type="file" name="pdf" accept="application/pdf"
                         class="block w-full text-sm border rounded-md p-2 bg-white">
                  <p class="text-xs text-gray-500 mt-1">Choisis ton PDF puis clique “Envoyer”.</p>
                </div>

                <div>
                  <label class="block text-sm text-gray-700 mb-1">Commentaire (optionnel)</label>
                  <textarea name="content" rows="3" class="w-full border rounded-md p-2 text-sm"></textarea>
                </div>

                <div class="flex gap-2">
                  <button type="submit"
                          class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                    Envoyer
                  </button>
                  <button type="button"
                          class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm hover:bg-gray-300 transition">
                    Annuler
                  </button>
                </div>

                
              </form>
            </div>
          </div>
        </div>
      @endif
    </main>
  </div>
</div>
@endsection
