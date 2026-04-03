@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">{{ $module->name }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Tentative {{ $attempt->attempt }}</p>
        </div>
        @if($module->timelimit)
            <div class="text-right">
                <div class="text-xs text-gray-500 mb-1">Temps restant</div>
                <div id="timer" class="text-xl font-mono font-bold text-gray-800"></div>
            </div>
        @endif
    </div>

    <form action="{{ route('quiz.submit', $module->id) }}" method="POST" id="quiz-attempt-form">
        @csrf

        <div class="space-y-6">
            @foreach($questions as $index => $question)
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded
                                {{ $question->qtype === 'truefalse'
                                    ? 'bg-indigo-100 text-indigo-700'
                                    : 'bg-blue-100 text-blue-700' }}">
                                {{ $question->qtype === 'truefalse' ? 'Vrai/Faux' : 'QCM' }}
                            </span>
                            <span class="text-sm font-semibold text-gray-800">Question {{ $index + 1 }}</span>
                        </div>
                        <span class="text-xs text-gray-500 shrink-0">{{ $question->defaultmark }} pt(s)</span>
                    </div>

                    <p class="text-sm text-gray-800 mb-4 leading-relaxed">{{ $question->questiontext }}</p>

                    <div class="space-y-2">
                        @foreach($question->answers as $answer)
                            @php
                                $isGiven = isset($givenAnswers[$question->id])
                                    && $givenAnswers[$question->id]->answer_id == $answer->id;
                            @endphp
                            <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer
                                          hover:bg-blue-50 hover:border-blue-300 transition
                                          {{ $isGiven ? 'bg-blue-50 border-blue-400' : 'border-gray-200 bg-gray-50' }}">
                                <input type="radio"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $answer->id }}"
                                       class="shrink-0"
                                       {{ $isGiven ? 'checked' : '' }}>
                                <span class="text-sm text-gray-800">{{ $answer->answer }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('quiz.show', $module->id) }}"
               class="text-sm text-gray-500 hover:text-gray-700 transition"
               onclick="return confirm('Quitter sans soumettre ? Votre progression en cours sera conservée.')">
                Quitter
            </a>
            <button type="submit"
                    onclick="return confirm('Soumettre le quiz ? Vous ne pourrez plus modifier vos réponses.')"
                    class="bg-blue-600 text-white px-8 py-2.5 rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                Soumettre le quiz
            </button>
        </div>
    </form>
</div>

@if($module->timelimit)
<script>
    const startTime = {{ $attempt->timestart->timestamp }};
    const timeLimit = {{ $module->timelimit }};
    const endTime   = startTime + timeLimit;

    function updateTimer() {
        const now       = Math.floor(Date.now() / 1000);
        const remaining = endTime - now;

        if (remaining <= 0) {
            document.getElementById('timer').textContent = '00:00';
            document.getElementById('quiz-attempt-form').submit();
            return;
        }

        const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
        const seconds = (remaining % 60).toString().padStart(2, '0');
        document.getElementById('timer').textContent = `${minutes}:${seconds}`;
        document.getElementById('timer').className =
            'text-xl font-mono font-bold ' + (remaining < 300 ? 'text-red-600' : 'text-gray-800');
    }

    updateTimer();
    setInterval(updateTimer, 1000);
</script>
@endif
@endsection