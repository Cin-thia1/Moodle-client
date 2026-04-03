@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    @php
        $totalMarks = $module->quizQuestions->sum('defaultmark');
        $percentage = $totalMarks > 0 ? round(($attempt->sumgrades / $totalMarks) * 100) : 0;
        $passed     = $percentage >= 50;
    @endphp

    {{-- Score global --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-semibold">Quiz</span>
                    <h1 class="text-lg font-bold text-gray-800">{{ $module->name }}</h1>
                </div>
                <p class="text-sm text-gray-500">
                    Tentative {{ $attempt->attempt }} •
                    {{ $attempt->timefinish
                        ? \Carbon\Carbon::parse($attempt->timefinish)->setTimezone(config('app.timezone'))->format('d/m/Y à H:i')
                        : '—' }}
                </p>
            </div>

            {{-- Cercle score --}}
            <div class="shrink-0 text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center border-4
                    {{ $passed ? 'border-green-500 bg-green-50' : 'border-red-400 bg-red-50' }}">
                    <div>
                        <div class="text-xl font-bold {{ $passed ? 'text-green-700' : 'text-red-600' }}">
                            {{ $grade }}
                        </div>
                        <div class="text-xs text-gray-500">/{{ $module->grade }}</div>
                    </div>
                </div>
                <div class="text-xs font-semibold mt-2 {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                    {{ $passed ? '✓ Réussi' : '✗ Non réussi' }}
                </div>
            </div>
        </div>

        {{-- Barre progression --}}
        <div class="bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="h-3 rounded-full {{ $passed ? 'bg-green-500' : 'bg-red-500' }}"
                 style="width: {{ $percentage }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>0</span>
            <span class="font-medium {{ $passed ? 'text-green-600' : 'text-red-500' }}">{{ $percentage }}%</span>
            <span>100%</span>
        </div>

        {{-- Résumé --}}
        @php
            $correctCount = 0;
            $noAnswerCount = 0;
            foreach($module->quizQuestions as $q) {
                $aa = $attempt->answers->firstWhere('question_id', $q->id);
                if (!$aa || !$aa->answer) {
                    $noAnswerCount++;
                } elseif ($aa->answer->fraction >= 1.0) {
                    $correctCount++;
                }
            }
            $total      = $module->quizQuestions->count();
            $wrongCount = $total - $correctCount - $noAnswerCount;
        @endphp
        <div class="flex gap-4 mt-4 flex-wrap">
            <div class="flex items-center gap-1.5 text-sm text-green-700">
                <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center text-xs font-bold">✓</span>
                {{ $correctCount }} bonne(s)
            </div>
            <div class="flex items-center gap-1.5 text-sm text-red-600">
                <span class="w-5 h-5 bg-red-100 rounded-full flex items-center justify-center text-xs font-bold">✗</span>
                {{ $wrongCount }} mauvaise(s)
            </div>
            @if($noAnswerCount > 0)
                <div class="flex items-center gap-1.5 text-sm text-gray-500">
                    <span class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center text-xs">—</span>
                    {{ $noAnswerCount }} sans réponse
                </div>
            @endif
        </div>
    </div>

    {{-- Détail question par question --}}
    <div class="space-y-4">
        @foreach($module->quizQuestions->sortBy('slot') as $index => $question)
            @php
                $attemptAnswer = $attempt->answers->firstWhere('question_id', $question->id);
                $givenAnswer   = $attemptAnswer?->answer;
                $isCorrect     = $givenAnswer && $givenAnswer->fraction >= 1.0;
                $noAnswer      = !$givenAnswer;
            @endphp

            <div class="bg-white rounded-lg shadow overflow-hidden">
                {{-- Barre couleur en haut --}}
                <div class="h-1.5 {{ $isCorrect ? 'bg-green-500' : ($noAnswer ? 'bg-gray-300' : 'bg-red-500') }}"></div>

                <div class="p-5">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="shrink-0">
                            @if($isCorrect)
                                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">✓</div>
                            @elseif($noAnswer)
                                <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm">—</div>
                            @else
                                <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">✗</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span class="text-xs text-gray-500">Question {{ $index + 1 }}</span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                    {{ $question->qtype === 'truefalse' ? 'Vrai/Faux' : 'QCM' }}
                                </span>
                                <span class="text-xs font-semibold ml-auto {{ $isCorrect ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $isCorrect ? '+' . $question->defaultmark : '0' }}/{{ $question->defaultmark }} pt(s)
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 leading-relaxed">
                                {{ $question->questiontext }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2 ml-11">
                        @foreach($question->answers as $answer)
                            @php
                                $isGiven      = $givenAnswer && $givenAnswer->id === $answer->id;
                                $isCorrectAns = $answer->fraction >= 1.0;
                            @endphp
                            <div class="flex items-center gap-3 px-4 py-2.5 rounded-lg border text-sm
                                @if($isGiven && $isCorrectAns) bg-green-50 border-green-400
                                @elseif($isGiven && !$isCorrectAns) bg-red-50 border-red-400
                                @elseif(!$isGiven && $isCorrectAns) bg-green-50 border-green-300 border-dashed
                                @else bg-gray-50 border-gray-200
                                @endif">

                                <div class="shrink-0">
                                    @if($isGiven && $isCorrectAns)
                                        <span class="w-5 h-5 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</span>
                                    @elseif($isGiven && !$isCorrectAns)
                                        <span class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold">✗</span>
                                    @elseif(!$isGiven && $isCorrectAns)
                                        <span class="w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs font-bold">✓</span>
                                    @else
                                        <span class="w-5 h-5 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center text-xs">○</span>
                                    @endif
                                </div>

                                <span class="flex-1
                                    @if($isGiven && $isCorrectAns) text-green-800 font-semibold
                                    @elseif($isGiven && !$isCorrectAns) text-red-700 font-semibold
                                    @elseif(!$isGiven && $isCorrectAns) text-green-700
                                    @else text-gray-500
                                    @endif">
                                    {{ $answer->answer }}
                                </span>

                                @if($isGiven && $isCorrectAns)
                                    <span class="shrink-0 text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                        ✓ Votre réponse — correct
                                    </span>
                                @elseif($isGiven && !$isCorrectAns)
                                    <span class="shrink-0 text-xs font-medium bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                                        ✗ Votre réponse — incorrect
                                    </span>
                                @elseif(!$isGiven && $isCorrectAns)
                                    <span class="shrink-0 text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                        Bonne réponse
                                    </span>
                                @endif
                            </div>
                        @endforeach

                        @if($noAnswer)
                            <p class="text-xs text-gray-400 italic mt-1">Aucune réponse donnée.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-between items-center">
        <a href="{{ route('quiz.show', $module->id) }}"
           class="text-sm text-blue-600 hover:underline">
            ← Retour au quiz
        </a>
        @if($canAttempt ?? false)
            <a href="{{ route('quiz.attempt', $module->id) }}"
               class="bg-blue-600 text-white px-5 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                Nouvelle tentative
            </a>
        @endif
    </div>

</div>
@endsection