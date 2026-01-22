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
          @forelse($courses as $course)
            <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
               class="block px-3 py-2 rounded-md text-sm hover:bg-gray-50 text-gray-700 transition">
              {{ $course->fullname }}
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

      {{-- HEADER DEVOIR --}}
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-800 truncate">{{ $module->name }}</h1>

            {{-- intro / description --}}
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
                Barème : {{ $module->grade ?? '—' }}
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

          {{-- Bouton gradebook : enseignant seulement --}}
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
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Contenu</th>
                  <th class="text-left px-4 py-3 font-semibold text-gray-600">Soumis le</th>
                </tr>
              </thead>

              <tbody class="divide-y">
                @forelse($students as $student)
                  @php
                    $sub = $subByUser[$student->id] ?? null;
                    $status = $sub?->status ?? 'draft';
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
                        <a class="text-blue-600 hover:underline"
                           target="_blank"
                           href="{{ asset($sub->file) }}">
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
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                      Aucun étudiant inscrit.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t text-xs text-gray-500">
            Moodle-like (enseignant).
          </div>
        </div>

      @else
      {{-- ========================= --}}
      {{-- ========= ELEVE ========= --}}
      {{-- ========================= --}}

        @php
          // Si tu veux plus tard : récupérer la submission de l'élève depuis backend
          // Pour l’instant, UI seulement.
          $mySub = null;
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
                    <td class="px-4 py-3 font-semibold text-gray-700 w-64">Statut</td>
                    <td class="px-4 py-3">
                      <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                        UI en attente de la vraie soumission
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            {{-- Zone “remettre mon travail” (UI) --}}
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-gray-800">Remettre mon travail</h3>
                <span class="text-xs text-gray-500">(PDF)</span>
              </div>

              <form action="#" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3">
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

                <p class="text-xs text-gray-500">
                  UI uniquement pour l’instant : pas encore branché sur la route de soumission.
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
