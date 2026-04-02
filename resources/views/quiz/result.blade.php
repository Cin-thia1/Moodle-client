@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

  {{-- Score global --}}
  @php
    $totalMarks   = $module->quizQuestions->sum('defaultmark');
    $percentage   = $totalMarks > 0 ? round(($attempt->sumgrades / $totalMarks) * 100) : 0;
    $passed       = $percentage >= 50;
  @endphp

  <div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-gray-800">{{ $module->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">
          Tentative {{ $attempt->attempt }} •
          {{ $attempt->timefinish ? \Carbon\Carbon::parse($attempt->timefinish)->format('d/m/Y H:i') : '—' }}
        </p>
      </div>

      <div class="text-center shrink-0">
        <div class="text-3xl font-bold {{ $passed ? 'text-green-600' : 'text-red-600' }}">
          {{ $grade }}/{{ $module->grade }}
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ $percentage }}%</div>
        <div class="text-xs font-semibold mt-1 {{ $passed ? 'text-green-600' : 'text-red-600' }}">
          {{ $passed ? '✓ Réussi' : '✗ Non réussi' }}
        </div>
      </div>
    </div>

    {{-- Barre de progression --}}
    <div class="mt-4 bg-gray-100 rounded-full h-2">
      <div class="h-2 rounded-full {{ $passed ? 'bg-green-500' : 'bg-red-500' }} transition-all"
           style="width: {{ $percentage }}%"></div>
    </div>
  </div>

  {{-- Détail question par question --}}
  <div class="space-y-4">
    @foreach($module->quizQuestions as $index => $question)
      @php
        $attemptAnswer = $attempt->answers->firstWhere('question_id', $question->id);
        $givenAnswer   = $attemptAnswer?->answer;
        $isCorrect     = $givenAnswer && $givenAnswer->isCorrect();
        $correctAnswer = $question->answers->firstWhere('fraction', 1.0);
      @endphp

      <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start gap-3 mb-3">
          <div class="shrink-0 mt-0.5">
            @if($isCorrect)
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700 text-xs font-bold">✓</span>
            @elseif($givenAnswer)
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs font-bold">✗</span>
            @else
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 text-xs">—</span>
            @endif
          </div>
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs text-gray-500">Question {{ $index + 1 }}</span>
              <span class="text-xs font-semibold {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                {{ $isCorrect ? '+' . $question->defaultmark : '0' }}/{{ $question->defaultmark }} pt(s)
              </span>
            </div>
            <p class="text-sm text-gray-800 font-medium">{{ $question->questiontext }}</p>
          </div>
        </div>

        <div class="space-y-2 ml-9">
          @foreach($question->answers as $answer)
            @php
              $isGiven   = $givenAnswer && $givenAnswer->id === $answer->id;
              $isCorrectA = $answer->isCorrect();
            @endphp
            <div class="flex items-center gap-2 px-3 py-2 rounded-md text-sm
              {{ $isCorrectA ? 'bg-green-50 border border-green-200' : '' }}
              {{ $isGiven && !$isCorrectA ? 'bg-red-50 border border-red-200' : '' }}
              {{ !$isGiven && !$isCorrectA ? 'text-gray-500' : '' }}">
              <span class="text-xs">
                @if($isGiven && $isCorrectA) ✓
                @elseif($isGiven && !$isCorrectA) ✗
                @elseif($isCorrectA) ✓
                @else ○
                @endif
              </span>
              <span>{{ $answer->answer }}</span>
              @if($isGiven && !$isCorrectA)
                <span class="text-xs text-red-500 ml-auto">Votre réponse</span>
              @endif
              @if($isCorrectA)
                <span class="text-xs text-green-600 ml-auto">Bonne réponse</span>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-6 flex justify-between items-center">
    <a href="{{ route('quiz.show', $module->id) }}"
       class="text-sm text-blue-600 hover:underline">
      ← Retour au quiz
    </a>
  </div>

</div>
@endsection