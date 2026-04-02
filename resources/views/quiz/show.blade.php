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

            {{-- Quiz du cours --}}
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

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
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
                                    Ouverture : {{ \Carbon\Carbon::parse($module->timeopen)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                            @if($module->timeclose)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    Fermeture : {{ \Carbon\Carbon::parse($module->timeclose)->format('d/m/Y H:i') }}
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
                               class="inline-flex items-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-md text-sm hover:bg-amber-600 transition">
                                Modifier
                            </a>
                            <form action="{{ route('quiz.destroy', $module->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce quiz et toutes les tentatives ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1 bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- VUE ENSEIGNANT --}}
            @if($isTeacher)

                {{-- QUESTIONS DU QUIZ - BONNES RÉPONSES EN VERT --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Questions du quiz 
                            <span class="text-gray-400 font-normal">({{ $module->quizQuestions->count() }})</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Les bonnes réponses sont mises en évidence en vert.</p>
                    </div>

                    <div class="divide-y">
                        @forelse($module->quizQuestions->sortBy('slot') as $q)
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    <span class="shrink-0 text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded mt-1 min-w-[2.25rem] text-center">
                                        Q{{ $q->slot }}
                                    </span>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded font-medium">
                                                {{ $q->qtype === 'truefalse' ? 'Vrai / Faux' : 'QCM' }}
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $q->defaultmark ?? 1 }} pt(s)</span>
                                        </div>

                                        <p class="text-base text-gray-800 font-medium leading-relaxed mb-5">
                                            {{ $q->questiontext }}
                                        </p>

                                        <!-- Réponses avec mise en évidence forte des bonnes réponses -->
                                        <div class="space-y-3">
                                            @foreach($q->answers as $ans)
                                                @php
                                                    $isCorrect = $ans->fraction > 0;
                                                @endphp

                                                <div class="flex items-start gap-4 p-4 rounded-2xl border transition-all
                                                            {{ $isCorrect 
                                                                ? 'bg-green-50 border-green-400 shadow-sm' 
                                                                : 'bg-gray-50 border-gray-200' }}">

                                                    <!-- Icône -->
                                                    <div class="shrink-0 mt-0.5">
                                                        @if($isCorrect)
                                                            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-2xl font-bold shadow">
                                                                ✓
                                                            </div>
                                                        @else
                                                            <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-xl">
                                                                ✕
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Texte de la réponse -->
                                                    <div class="flex-1 pt-1">
                                                        <span class="text-[15.5px] leading-relaxed 
                                                                    {{ $isCorrect ? 'text-green-800 font-semibold' : 'text-gray-700' }}">
                                                            {{ $ans->answer }}
                                                        </span>
                                                    </div>

                                                    <!-- Badge bonne réponse -->
                                                    @if($isCorrect)
                                                        <div class="shrink-0 self-center">
                                                            <span class="inline-flex items-center px-3.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                                ✓ Bonne réponse
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-gray-500">
                                Aucune question dans ce quiz.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RÉSULTATS DES ÉTUDIANTS --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">Résultats des étudiants</h2>
                        <p class="text-xs text-gray-500 mt-1">Méthode de notation : {{ $module->gradeMethodLabel() ?? 'Standard' }}</p>
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

            @else
                {{-- VUE ÉLÈVE (temporaire) --}}
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    Vue élève en cours de développement...
                </div>
            @endif

        </main>
    </div>
</div>
@endsection