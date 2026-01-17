@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid grid-cols-12 gap-6">

    {{-- SIDEBAR --}}
    <aside class="col-span-12 md:col-span-3 space-y-4">
      {{-- COURS --}}
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>
        <div class="space-y-2">
          @foreach($courses as $course)
            <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
               class="block px-3 py-2 rounded-md text-sm hover:bg-gray-50 text-gray-700 transition">
              {{ $course->fullname }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- DEVOIRS --}}
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Devoirs</h2>
        <div class="space-y-2">
          @foreach($assignments as $a)
            <a href="{{ route('assignments.show', $a->id) }}"
               class="block px-3 py-2 rounded-md text-sm transition
                 {{ (int)$module->id === (int)$a->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
              {{ $a->name }}
            </a>
          @endforeach
        </div>
      </div>
    </aside>

    {{-- CONTENU --}}
    <main class="col-span-12 md:col-span-9">

      {{-- HEADER DEVOIR --}}
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-800 truncate">{{ $module->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>

            <div class="flex flex-wrap gap-2 mt-3">
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                Ouvert : (UI)
              </span>
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                À rendre : {{ \Carbon\Carbon::parse($module->due_date)->format('d/m/Y H:i') }}
              </span>
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                Barème : {{ $module->max_grade }}
              </span>
            </div>

            {{-- ✅ ÉNONCÉ (PDF du prof) --}}
            @if(!empty($module->file_path))
              <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between gap-4">
                  <div>
                    <div class="text-sm font-semibold text-gray-800">Énoncé du devoir</div>
                    <div class="text-xs text-gray-500 mt-1">
                      Télécharge le fichier fourni par l’enseignant.
                    </div>
                  </div>

                  <a href="{{ $module->file_path }}"
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

          {{-- Bouton gradebook : enseignant seulement --}}
          @if(auth()->user()->hasRole('ROLE_TEACHER'))
            <a href="{{ route('courses.gradebook', $module->course_id) }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition shrink-0">
              Afficher carnet de notes
            </a>
          @endif
        </div>
      </div>

      {{-- ========================= --}}
      {{-- ======= ENSEIGNANT ====== --}}
      {{-- ========================= --}}
      @if(auth()->user()->hasRole('ROLE_TEACHER'))
        <div class="bg-white rounded-lg shadow mt-6">
          <div class="p-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Travaux remis</h2>
            <p class="text-xs text-gray-500 mt-1">Tableau de correction (UI uniquement).</p>
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
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Note</th>
                </tr>
              </thead>

              <tbody class="divide-y">
                @foreach($students as $student)
                  @php
                    $sub = $subByUser[$student->id] ?? null;
                    $status = $sub?->status ?? 'not_submitted';
                  @endphp

                  <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $student->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $student->email }}</td>

                    <td class="px-4 py-3">
                      @if($status === 'submitted')
                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">Remis</span>
                      @elseif($status === 'graded')
                        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">Noté</span>
                      @else
                        <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700">Pas remis</span>
                      @endif
                    </td>

                    <td class="px-4 py-3">
                      {{ $sub?->file ?? '—' }}
                      @if($sub?->file)
                        <div class="text-xs text-gray-400">(lien fictif)</div>
                      @endif
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                      {{ $sub?->comment ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <input type="number"
                               min="0"
                               max="{{ $module->max_grade }}"
                               value="{{ $sub?->grade ?? '' }}"
                               class="w-24 border rounded-md px-2 py-1 text-sm"
                               @if(!$sub) disabled @endif>

                        <button type="button"
                                class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-blue-700 transition
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$sub) disabled @endif>
                          Enregistrer
                        </button>
                      </div>

                      @if(!$sub)
                        <div class="text-xs text-gray-400 mt-1">Pas de soumission</div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t text-xs text-gray-500">
            UI Moodle-like (enseignant).
          </div>
        </div>

      @else
      {{-- ========================= --}}
      {{-- ========= ELEVE ========= --}}
      {{-- ========================= --}}

        @php
          // MOCK: statut et note élève (à remplacer par vraie submission plus tard)
          // not_submitted | submitted | graded
          $myStatus = 'submitted';
          $myFile = 'Mon_devoir.pdf';
          $mySubmittedAt = '16 janvier 2026, 20:30';
          $myGrade = null; // exemple: 14 si noté
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
                      @if($myStatus === 'submitted' || $myStatus === 'graded')
                        <div class="flex items-center justify-between gap-4">
                          <span class="text-blue-600 hover:underline cursor-pointer">{{ $myFile }}</span>
                          <span class="text-xs text-gray-500">{{ $mySubmittedAt }}</span>
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
                        <span class="font-semibold text-gray-900">{{ $myGrade }}/{{ $module->max_grade }}</span>
                      @else
                        <span class="text-gray-500">—</span>
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            {{-- Zone “remettre mon travail” --}}
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-gray-800">Remettre mon travail</h3>
                <span class="text-xs text-gray-500">(PDF)</span>
              </div>

              <form action="#" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3">
                @csrf
                <div>
                  <label class="block text-sm text-gray-700 mb-1">Fichier</label>
                  <input type="file" accept="application/pdf"
                         class="block w-full text-sm border rounded-md p-2 bg-white">
                  <p class="text-xs text-gray-500 mt-1">Choisis ton fichier PDF puis clique “Envoyer”.</p>
                </div>

                <div>
                  <label class="block text-sm text-gray-700 mb-1">Commentaire (optionnel)</label>
                  <textarea rows="3" class="w-full border rounded-md p-2 text-sm"></textarea>
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

                <p class="text-xs text-gray-500">
                  Front uniquement pour l’instant : aucune soumission n’est réellement enregistrée.
                </p>
              </form>
            </div>
          </div>
        </div>
      @endif
    </main>
  </div>
</div>
@endsection
