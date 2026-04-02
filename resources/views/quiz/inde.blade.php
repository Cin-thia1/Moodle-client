@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-12 gap-6">

        {{-- SIDEBAR --}}
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

            {{-- Boutons Ajouter - seulement enseignant --}}
            @if($isTeacher)
                <div class="mt-4 space-y-2">
                    <a href="{{ route('assignments.create', ['course_id' => $selectedCourseId]) }}"
                       class="w-full block text-center bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        + Ajouter un devoir
                    </a>
                    <a href="{{ route('quizzes.create', ['course_id' => $selectedCourseId]) }}"
                       class="w-full block text-center bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                        + Ajouter un quiz
                    </a>
                </div>
            @endif
        </aside>

        {{-- CONTENU PRINCIPAL --}}
        <main class="col-span-12 md:col-span-9">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">Évaluations</h1>
                            <p class="text-xs text-gray-500">
                                @if($isTeacher)
                                    Liste des devoirs et quizzes du cours sélectionné.
                                @else
                                    Remettez vos travaux et consultez vos notes.
                                @endif
                            </p>
                        </div>
                        <div class="w-64 hidden md:block">
                            <input type="text" id="searchEvaluations"
                                   placeholder="Rechercher un devoir ou un quiz..."
                                   class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring">
                        </div>
                    </div>
                </div>

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="mx-4 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="p-4">
                    @if($assignments->isEmpty())
                        <div class="text-sm text-gray-600 bg-gray-50 rounded-md p-4 text-center">
                            Aucun devoir ni quiz pour ce cours.
                        </div>
                    @else
                        @foreach($assignments as $a)
                            @php
                                $isQuiz = $a->modname === 'quiz';
                            @endphp

                            <div class="evaluation-item px-3 py-4 rounded-md hover:bg-gray-50 transition border-l-4 
                                        {{ $isQuiz ? 'border-indigo-500 bg-indigo-50/30' : 'border-blue-500' }}"
                                 data-name="{{ strtolower($a->name) }}">

                                <div class="flex items-start justify-between gap-4">
                                    {{-- Infos principales --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            @if($isQuiz)
                                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-medium">QUIZ</span>
                                            @else
                                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-medium">DEVOIR</span>
                                            @endif

                                            <a href="{{ $isQuiz ? route('quizzes.show', $a->id) : route('assignments.show', $a->id) }}"
                                               class="font-semibold text-gray-800 hover:underline truncate">
                                                {{ $a->name }}
                                            </a>
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($isQuiz)
                                                @if($a->timeopen)
                                                    Ouvert : <span class="font-medium">{{ \Carbon\Carbon::parse($a->timeopen)->format('d/m/Y H:i') }}</span>
                                                @endif
                                                @if($a->timeclose)
                                                    • Fermé le : <span class="font-medium">{{ \Carbon\Carbon::parse($a->timeclose)->format('d/m/Y H:i') }}</span>
                                                @endif
                                                @if($a->timelimit)
                                                    • ⏱ Durée : <span class="font-medium">{{ floor($a->timelimit / 60) }} min</span>
                                                @endif
                                                • Note max : <span class="font-medium">{{ $a->grade ?? 20 }}</span>
                                            @else
                                                Date limite : <span class="font-medium text-gray-700">
                                                    {{ $a->duedate ? \Carbon\Carbon::parse($a->duedate)->format('d/m/Y H:i') : '—' }}
                                                </span>
                                                • Barème : <span class="font-medium">{{ $a->grade ?? '—' }}</span>
                                            @endif
                                        </div>

                                        {{-- Statut pour les élèves (uniquement devoirs) --}}
                                        @if(!$isTeacher && !$isQuiz)
                                            @php $sub = $mySubs[$a->id] ?? null; @endphp
                                            @if($sub)
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @if($sub->status === 'submitted')
                                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Remis</span>
                                                    @elseif($sub->status === 'graded')
                                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Noté</span>
                                                    @endif
                                                    @if($sub->grade !== null)
                                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                                            Note : {{ $sub->grade }}/{{ $a->grade ?? 100 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded mt-2 inline-block">Pas encore remis</span>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="shrink-0 flex items-center gap-1">
                                        <a href="{{ $isQuiz ? route('quizzes.show', $a->id) : route('assignments.show', $a->id) }}"
                                           class="text-sm font-medium text-blue-600 hover:underline px-3 py-1">
                                            Ouvrir
                                        </a>

                                        @if($isTeacher)
                                            <span class="text-gray-300 select-none px-0.5">|</span>
                                            <a href="{{ $isQuiz ? route('quizzes.edit', $a->id) : route('assignments.edit', $a->id) }}"
                                               class="text-xs text-amber-600 hover:text-amber-800 px-2 py-1">
                                                Modifier
                                            </a>

                                            <form action="{{ $isQuiz ? route('quizzes.destroy', $a->id) : route('assignments.destroy', $a->id) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer « {{ addslashes($a->name) }} » ? Cette action est irréversible.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 px-2 py-1">
                                                    Supprimer
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
    const input = document.getElementById('searchEvaluations');
    if (!input) return;

    const items = document.querySelectorAll('.evaluation-item');
    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        items.forEach(item => {
            item.style.display = item.dataset.name.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endsection