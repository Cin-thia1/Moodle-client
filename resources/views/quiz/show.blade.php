@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-12 gap-6">

        {{-- SIDEBAR --}}
        <aside class="col-span-12 md:col-span-3 space-y-4">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Mes matières</h2>
                <div class="space-y-2">
                    @foreach($courses as $course)
                        <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md text-sm transition
                             {{ $course->id == $module->section->course->id
                                ? 'bg-blue-50 text-blue-700 font-semibold'
                                : 'hover:bg-gray-50 text-gray-700' }}">
                            <span class="truncate">{{ $course->fullname }}</span>
                            @if($course->id == $module->section->course->id)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Actif</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Quiz du cours</h2>
                <div class="space-y-2">
                    @forelse($quizzes as $q)
                        <a href="{{ route('quiz.show', $q->id) }}"
                           class="block px-3 py-2 rounded-md text-sm transition
                             {{ $q->id == $module->id
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'hover:bg-gray-50 text-gray-700' }}">
                            {{ $q->name }}
                        </a>
                    @empty
                        <div class="text-sm text-gray-500">Aucun quiz.</div>
                    @endforelse
                </div>
            </div>
        </aside>

        {{-- CONTENU PRINCIPAL --}}
        <main class="col-span-12 md:col-span-9 space-y-6">

            {{-- flash messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- HEADER DU QUIZ --}}
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-semibold">Quiz</span>
                            <h1 class="text-xl font-bold text-gray-800 truncate">{{ $module->name }}</h1>
                        </div>

                        @if($module->intro)
                            <p class="text-sm text-gray-600 mt-1">{{ $module->intro }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($module->timeopen)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    Ouverture : {{ \Carbon\Carbon::parse($module->timeopen)->setTimezone(config('app.timezone'))->format('d/m/Y H:i') }}
                                </span>
                            @endif
                            @if($module->timeclose)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    Fermeture : {{ \Carbon\Carbon::parse($module->timeclose)->setTimezone(config('app.timezone'))->format('d/m/Y H:i') }}
                                </span>
                            @endif
                            @if($module->timelimit)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    ⏱ {{ $module->timelimitFormatted() }}
                                </span>
                            @endif
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                Note max : {{ $module->grade }}
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                {{ $module->attempts == 0 ? 'Tentatives illimitées' : $module->attempts . ' tentative(s)' }}
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                {{ $module->quizQuestions->count() }} question(s)
                            </span>
                        </div>
                    </div>

                    @if($isTeacher)
                        <div class="flex flex-col gap-2 shrink-0">
                            <a href="{{ route('quiz.edit', $module->id) }}"
                               class="inline-flex items-center justify-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-md text-sm hover:bg-amber-600 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Modifier
                            </a>
                            <form action="{{ route('quiz.destroy', $module->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce quiz et toutes les tentatives ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1 bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ════════ VUE ENSEIGNANT ════════ --}}
            @if($isTeacher)

                {{-- Aperçu des questions --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Questions du quiz
                            <span class="text-gray-400 font-normal">({{ $module->quizQuestions->count() }})</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Les bonnes réponses sont surlignées en vert.</p>
                    </div>

                    <div class="divide-y">
                        @forelse($module->quizQuestions->sortBy('slot') as $q)
                            <div class="p-5">
                                <div class="flex items-start gap-4">
                                    <span class="shrink-0 text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded mt-0.5 min-w-[2.25rem] text-center">
                                        Q{{ $q->slot }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-medium">
                                                {{ $q->qtype === 'truefalse' ? 'Vrai / Faux' : 'QCM' }}
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $q->defaultmark ?? 1 }} pt(s)</span>
                                        </div>

                                        <p class="text-sm font-semibold text-gray-800 mb-3">{{ $q->questiontext }}</p>

                                        <div class="space-y-2">
                                            @foreach($q->answers as $ans)
                                                @php $isCorrect = $ans->fraction > 0; @endphp
                                                <div class="flex items-center gap-3 px-4 py-2.5 rounded-lg border
                                                    {{ $isCorrect
                                                        ? 'bg-green-50 border-green-400'
                                                        : 'bg-gray-50 border-gray-200' }}">
                                                    <div class="shrink-0">
                                                        @if($isCorrect)
                                                            <div class="w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</div>
                                                        @else
                                                            <div class="w-6 h-6 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-xs">✕</div>
                                                        @endif
                                                    </div>
                                                    <span class="text-sm flex-1 {{ $isCorrect ? 'text-green-800 font-semibold' : 'text-gray-700' }}">
                                                        {{ $ans->answer }}
                                                    </span>
                                                    @if($isCorrect)
                                                        <span class="ml-auto text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full shrink-0">
                                                            Bonne réponse
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center text-gray-500 text-sm">Aucune question dans ce quiz.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Résultats des étudiants --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">Résultats des étudiants</h2>
                        <p class="text-xs text-gray-500 mt-1">Méthode : {{ $module->gradeMethodLabel() }}</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Étudiant</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tentatives</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Meilleure note</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Détail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($students as $student)
                                    @php $r = $results[$student->id] ?? null; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $student->name }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $r ? $r['attempts']->where('state', 'finished')->count() : 0 }}
                                            / {{ $module->attempts == 0 ? '∞' : $module->attempts }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($r && $r['grade'] !== null)
                                                @php $pct = $module->grade > 0 ? round(($r['grade'] / $module->grade) * 100) : 0; @endphp
                                                <span class="font-semibold {{ $pct >= 50 ? 'text-green-700' : 'text-red-600' }}">
                                                    {{ $r['grade'] }}/{{ $module->grade }}
                                                </span>
                                                <span class="text-xs text-gray-400 ml-1">({{ $pct }}%)</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($r && $r['best'])
                                                <a href="{{ route('quiz.result', [$module->id, $r['best']->id]) }}"
                                                   class="text-blue-600 hover:underline text-xs">Voir détail</a>
                                            @else
                                                <span class="text-gray-400 text-xs">Pas de tentative</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 text-sm">
                                            Aucun étudiant inscrit à ce cours.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- ════════ VUE ÉLÈVE ════════ --}}
            @else
                @php
                    $finishedAttempts  = $myAttempts->where('state', 'finished');
                    $inProgressAttempt = $myAttempts->firstWhere('state', 'inprogress');
                    $doneCount         = $finishedAttempts->count();
                    $maxAttempts       = (int) $module->attempts;
                @endphp

                {{-- Statut global --}}
                <div class="bg-white rounded-lg shadow p-5">
                    <h2 class="text-sm font-semibold text-gray-800 mb-4">Mon statut</h2>

                    <div class="grid grid-cols-3 gap-4 text-center mb-5">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-2xl font-bold text-gray-800">{{ $doneCount }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Tentative(s) / {{ $maxAttempts == 0 ? '∞' : $maxAttempts }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            @php
                                $bestGrade = null;
                                if ($finishedAttempts->isNotEmpty()) {
                                    $best = $finishedAttempts->sortByDesc('sumgrades')->first();
                                    $bestGrade = $best->gradeOutOf($module->grade);
                                }
                            @endphp
                            @if($bestGrade !== null)
                                @php $pct = $module->grade > 0 ? round(($bestGrade / $module->grade) * 100) : 0; @endphp
                                <div class="text-2xl font-bold {{ $pct >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $bestGrade }}/{{ $module->grade }}
                                </div>
                            @else
                                <div class="text-2xl font-bold text-gray-400">—</div>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">Meilleure note</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            @php
                                $now       = now()->utc();
                                $topen     = $module->timeopen  ? \Carbon\Carbon::parse($module->timeopen)->utc()  : null;
                                $tclose    = $module->timeclose ? \Carbon\Carbon::parse($module->timeclose)->utc() : null;
                                $isOpenNow = (!$topen || $now->gte($topen)) && (!$tclose || $now->lte($tclose));
                            @endphp
                            <div class="text-2xl font-bold {{ $isOpenNow ? 'text-green-600' : 'text-red-500' }}">
                                {{ $isOpenNow ? 'Ouvert' : 'Fermé' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Statut</div>
                        </div>
                    </div>

                    {{-- Message de statut --}}
                    @if($statusMessage)
                        <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded text-sm">
                            {{ $statusMessage }}
                        </div>
                    @endif

                    {{-- Bouton passer / reprendre --}}
                    @if($inProgressAttempt)
                        <a href="{{ route('quiz.attempt', $module->id) }}"
                           class="inline-flex items-center gap-2 bg-amber-500 text-white px-6 py-2.5 rounded-md text-sm font-semibold hover:bg-amber-600 transition">
                            ▶ Reprendre la tentative en cours
                        </a>
                    @elseif($canAttempt)
                        <a href="{{ route('quiz.attempt', $module->id) }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                            {{ $doneCount === 0 ? '▶ Commencer le quiz' : '▶ Nouvelle tentative' }}
                        </a>
                    @endif
                </div>

                {{-- Historique des tentatives --}}
                @if($myAttempts->isNotEmpty())
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b">
                            <h2 class="text-sm font-semibold text-gray-800">Mes tentatives</h2>
                        </div>
                        <div class="divide-y">
                            @foreach($myAttempts->sortByDesc('attempt') as $att)
                                @php
                                    $attGrade = $att->isFinished() ? $att->gradeOutOf($module->grade) : null;
                                    $attPct   = ($attGrade !== null && $module->grade > 0)
                                                ? round(($attGrade / $module->grade) * 100) : null;
                                @endphp
                                <div class="px-5 py-4 flex items-center justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-800">
                                            Tentative {{ $att->attempt }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            @if($att->isFinished())
                                                Soumis le {{ \Carbon\Carbon::parse($att->timefinish)->setTimezone(config('app.timezone'))->format('d/m/Y à H:i') }}
                                            @else
                                                <span class="text-amber-600 font-medium">En cours</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        @if($attGrade !== null)
                                            <span class="text-sm font-semibold {{ $attPct >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $attGrade }}/{{ $module->grade }}
                                                <span class="text-xs font-normal text-gray-400">({{ $attPct }}%)</span>
                                            </span>
                                        @endif
                                        @if($att->isFinished())
                                            <a href="{{ route('quiz.result', [$module->id, $att->id]) }}"
                                               class="text-xs text-blue-600 hover:underline font-medium">
                                                Voir mes réponses →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </main>
    </div>
</div>
@endsection