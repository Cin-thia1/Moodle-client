@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="p-6 border-b bg-gray-50">
      <div class="flex items-center gap-3">
        <a href="{{ route('quiz.show', $module->id) }}"
           class="text-gray-500 hover:text-gray-800 text-sm transition">← Retour</a>
        <span class="text-gray-300">|</span>
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Modifier le Quiz</h2>
          <p class="text-sm text-gray-600 mt-1">Modifiez les paramètres et les questions du quiz.</p>
        </div>
      </div>
    </div>

    <div class="p-6">

      @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded text-sm">
          <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('quiz.update', $module->id) }}" method="POST" class="space-y-8" id="quiz-form">
        @csrf
        @method('PUT')

        {{-- ═══ Informations générales ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">
            Informations générales
          </h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
            <select name="course_id" class="w-full border rounded-lg px-3 py-2 text-sm" disabled>
              @foreach($courses as $course)
                <option value="{{ $course->id }}"
                  {{ (int)$selectedCourseId === (int)$course->id ? 'selected' : '' }}>
                  {{ $course->fullname }}
                </option>
              @endforeach
            </select>
            {{-- Champ caché car disabled ne soumet pas --}}
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
            <select name="section_id" class="w-full border rounded-lg px-3 py-2 text-sm">
              @forelse($sections as $sec)
                <option value="{{ $sec->id }}"
                  {{ (int)old('section_id', $module->section_id) === (int)$sec->id ? 'selected' : '' }}>
                  {{ $sec->name }}
                </option>
              @empty
                <option value="">Aucune section disponible</option>
              @endforelse
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du quiz <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $module->name) }}" required
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   placeholder="Ex : Quiz 1 – Algorithmique">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
            <textarea name="intro" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"
                      placeholder="Instructions pour les étudiants…">{{ old('intro', $module->intro) }}</textarea>
          </div>
        </div>

        {{-- ═══ Chronométrage ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">Chronométrage</h3>

          @php
            $timeopen  = $module->timeopen
              ? \Carbon\Carbon::parse($module->timeopen)->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i')
              : '';
            $timeclose = $module->timeclose
              ? \Carbon\Carbon::parse($module->timeclose)->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i')
              : '';
            $timelimit = $module->timelimit ? (int)($module->timelimit / 60) : '';
          @endphp

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ouverture du quiz</label>
              <input type="datetime-local" name="timeopen"
                     value="{{ old('timeopen', $timeopen) }}"
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fermeture du quiz</label>
              <input type="datetime-local" name="timeclose"
                     value="{{ old('timeclose', $timeclose) }}"
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
          </div>

          <div class="md:w-1/2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Durée limite (minutes) <span class="text-gray-400 text-xs">– laisser vide = illimité</span>
            </label>
            <input type="number" name="timelimit" value="{{ old('timelimit', $timelimit) }}" min="1"
                   class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ex : 60">
          </div>
        </div>

        {{-- ═══ Notes ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">Notes</h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Note maximale <span class="text-red-500">*</span></label>
              <input type="number" name="grade" value="{{ old('grade', $module->grade) }}" min="0" max="100" required
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tentatives autorisées</label>
              <select name="attempts" class="w-full border rounded-lg px-3 py-2 text-sm">
                @php $att = old('attempts', $module->attempts); @endphp
                <option value="1" {{ $att == 1 ? 'selected' : '' }}>1 tentative</option>
                <option value="2" {{ $att == 2 ? 'selected' : '' }}>2 tentatives</option>
                <option value="3" {{ $att == 3 ? 'selected' : '' }}>3 tentatives</option>
                <option value="0" {{ $att == 0 ? 'selected' : '' }}>Illimité</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de notation</label>
              <select name="grademethod" class="w-full border rounded-lg px-3 py-2 text-sm">
                @php $gm = old('grademethod', $module->grademethod); @endphp
                <option value="0" {{ $gm == 0 ? 'selected' : '' }}>Note la plus haute</option>
                <option value="1" {{ $gm == 1 ? 'selected' : '' }}>Moyenne</option>
                <option value="2" {{ $gm == 2 ? 'selected' : '' }}>Première tentative</option>
                <option value="3" {{ $gm == 3 ? 'selected' : '' }}>Dernière tentative</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="shuffleanswers" id="shuffleanswers" value="1"
                   class="rounded" {{ old('shuffleanswers', $module->shuffleanswers) ? 'checked' : '' }}>
            <label for="shuffleanswers" class="text-sm text-gray-700">
              Mélanger les réponses à l'intérieur des questions
            </label>
          </div>
        </div>

        {{-- ═══ Questions ═══ --}}
        <div class="space-y-4">
          <div class="flex items-center justify-between border-b pb-2">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Questions</h3>
            <div class="flex gap-2">
              <button type="button" onclick="addQuestion('multichoice')"
                      class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 transition">
                + QCM
              </button>
              <button type="button" onclick="addQuestion('truefalse')"
                      class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition">
                + Vrai/Faux
              </button>
            </div>
          </div>

          <div id="questions-container" class="space-y-4"></div>

          <div id="no-questions-msg" class="text-sm text-gray-400 text-center py-6 bg-gray-50 rounded-lg"
               style="display:none;">
            Aucune question. Cliquez sur "+ QCM" ou "+ Vrai/Faux" pour commencer.
          </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t">
          <a href="{{ route('quiz.show', $module->id) }}"
             class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
            Annuler
          </a>
          <button type="submit"
                  class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
            Enregistrer les modifications
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Questions existantes injectées en JSON pour le JS --}}
<script>
// ── Données existantes depuis PHP ────────────────────────────────
const existingQuestions = @json($module->quizQuestions->sortBy('slot')->values());

let questionCount = 0;
const answerCounts = {};

// ── Initialisation : charger les questions existantes ────────────
document.addEventListener('DOMContentLoaded', function () {
  if (existingQuestions.length === 0) {
    document.getElementById('no-questions-msg').style.display = '';
  }

  existingQuestions.forEach(function (q) {
    addQuestionFromData(q);
  });
});

// ── Ajoute une question pré-remplie depuis les données existantes ─
function addQuestionFromData(qData) {
  document.getElementById('no-questions-msg').style.display = 'none';
  const container = document.getElementById('questions-container');
  const idx = questionCount++;
  const type = qData.qtype;

  const typeBadgeColor = type === 'multichoice' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700';
  const typeLabel = type === 'multichoice' ? 'QCM' : 'Vrai / Faux';

  const div = document.createElement('div');
  div.className = 'question-block bg-gray-50 border rounded-lg p-4 space-y-3';
  div.dataset.idx = idx;

  div.innerHTML = `
    <input type="hidden" name="questions[${idx}][qtype]" value="${type}">

    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="text-xs font-semibold px-2 py-0.5 rounded ${typeBadgeColor}">${typeLabel}</span>
        <span class="text-xs text-gray-500">Question ${idx + 1}</span>
      </div>
      <button type="button" onclick="removeQuestion(this)"
              class="text-red-400 hover:text-red-600 text-xs transition">✕ Supprimer</button>
    </div>

    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Énoncé *</label>
      <textarea name="questions[${idx}][questiontext]" rows="2" required
                class="w-full border rounded-md px-3 py-2 text-sm"
                placeholder="Écrivez la question ici…">${escapeHtml(qData.questiontext)}</textarea>
    </div>

    <div class="w-32">
      <label class="block text-xs font-medium text-gray-600 mb-1">Points</label>
      <input type="number" name="questions[${idx}][defaultmark]"
             value="${qData.defaultmark ?? 1}" min="1"
             class="w-full border rounded-md px-3 py-2 text-sm">
    </div>

    ${type === 'truefalse' ? buildTrueFalseFromData(idx, qData.answers) : buildMultichoiceFromData(idx, qData.answers)}
  `;

  container.appendChild(div);
}

// ── Vrai/Faux pré-rempli ─────────────────────────────────────────
function buildTrueFalseFromData(idx, answers) {
  // La réponse "Vrai" est à fraction > 0 si correcte
  const vraiAnswer = answers ? answers.find(a => a.answer === 'Vrai') : null;
  const vraiCorrect = vraiAnswer && parseFloat(vraiAnswer.fraction) > 0;

  return `
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-2">Bonne réponse</label>
      <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="questions[${idx}][correct_answer]" value="0"
                 ${vraiCorrect ? 'checked' : ''}> Vrai
        </label>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="questions[${idx}][correct_answer]" value="1"
                 ${!vraiCorrect ? 'checked' : ''}> Faux
        </label>
      </div>
    </div>
  `;
}

// ── QCM pré-rempli ───────────────────────────────────────────────
function buildMultichoiceFromData(idx, answers) {
  if (!answers || answers.length === 0) {
    return buildMultichoice(idx); // fallback vide
  }

  answerCounts[idx] = answers.length;

  // Trouver l'index de la bonne réponse
  let correctIdx = 0;
  answers.forEach((a, i) => {
    if (parseFloat(a.fraction) > 0) correctIdx = i;
  });

  let rowsHtml = '';
  answers.forEach((a, aIdx) => {
    const isCorrect = aIdx === correctIdx;
    rowsHtml += buildAnswerRowWithValue(idx, aIdx, isCorrect, a.answer);
  });

  return `
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-medium text-gray-600">
          Réponses — cliquez le bouton radio pour marquer la bonne réponse
        </label>
        <button type="button" onclick="addAnswer(${idx})"
                class="text-xs text-blue-600 hover:underline">+ Ajouter une réponse</button>
      </div>

      <input type="hidden" name="questions[${idx}][correct_index]"
             id="correct-index-${idx}" value="${correctIdx}">

      <div id="answers-${idx}" class="space-y-2">
        ${rowsHtml}
      </div>
    </div>
  `;
}

// ── Ligne de réponse avec valeur pré-remplie ─────────────────────
function buildAnswerRowWithValue(qIdx, aIdx, isCorrect, value) {
  return `
    <div class="flex items-center gap-2 answer-row"
         id="answer-row-${qIdx}-${aIdx}"
         data-aidx="${aIdx}">
      <input type="radio"
             name="correct_radio_${qIdx}"
             value="${aIdx}"
             class="shrink-0"
             title="Marquer comme bonne réponse"
             ${isCorrect ? 'checked' : ''}
             onchange="setCorrectIndex(${qIdx}, ${aIdx})">

      <input type="text"
             name="questions[${qIdx}][answers][${aIdx}][answer]"
             required
             class="flex-1 border rounded-md px-3 py-1.5 text-sm"
             placeholder="Réponse ${aIdx + 1}"
             value="${escapeHtml(value ?? '')}">

      <input type="hidden"
             name="questions[${qIdx}][answers][${aIdx}][fraction]"
             id="fraction-${qIdx}-${aIdx}"
             value="${isCorrect ? '1' : '0'}">

      <button type="button" onclick="removeAnswer(this, ${qIdx})"
              class="text-red-400 hover:text-red-600 text-xs shrink-0">✕</button>
    </div>
  `;
}

// ── Ajouter une nouvelle question vide ───────────────────────────
function addQuestion(type) {
  document.getElementById('no-questions-msg').style.display = 'none';
  const container = document.getElementById('questions-container');
  const idx = questionCount++;

  const typeBadgeColor = type === 'multichoice' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700';
  const typeLabel = type === 'multichoice' ? 'QCM' : 'Vrai / Faux';

  const div = document.createElement('div');
  div.className = 'question-block bg-gray-50 border rounded-lg p-4 space-y-3';
  div.dataset.idx = idx;

  div.innerHTML = `
    <input type="hidden" name="questions[${idx}][qtype]" value="${type}">

    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="text-xs font-semibold px-2 py-0.5 rounded ${typeBadgeColor}">${typeLabel}</span>
        <span class="text-xs text-gray-500">Question ${idx + 1}</span>
      </div>
      <button type="button" onclick="removeQuestion(this)"
              class="text-red-400 hover:text-red-600 text-xs transition">✕ Supprimer</button>
    </div>

    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Énoncé *</label>
      <textarea name="questions[${idx}][questiontext]" rows="2" required
                class="w-full border rounded-md px-3 py-2 text-sm"
                placeholder="Écrivez la question ici…"></textarea>
    </div>

    <div class="w-32">
      <label class="block text-xs font-medium text-gray-600 mb-1">Points</label>
      <input type="number" name="questions[${idx}][defaultmark]" value="1" min="1"
             class="w-full border rounded-md px-3 py-2 text-sm">
    </div>

    ${type === 'truefalse' ? buildTrueFalse(idx) : buildMultichoice(idx)}
  `;

  container.appendChild(div);
}

// ── Vrai/Faux vide ───────────────────────────────────────────────
function buildTrueFalse(idx) {
  return `
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-2">Bonne réponse</label>
      <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="questions[${idx}][correct_answer]" value="0" checked> Vrai
        </label>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="questions[${idx}][correct_answer]" value="1"> Faux
        </label>
      </div>
    </div>
  `;
}

// ── QCM vide ─────────────────────────────────────────────────────
function buildMultichoice(idx) {
  answerCounts[idx] = 2;
  return `
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-medium text-gray-600">
          Réponses — cliquez le bouton radio pour marquer la bonne réponse
        </label>
        <button type="button" onclick="addAnswer(${idx})"
                class="text-xs text-blue-600 hover:underline">+ Ajouter une réponse</button>
      </div>

      <input type="hidden" name="questions[${idx}][correct_index]"
             id="correct-index-${idx}" value="0">

      <div id="answers-${idx}" class="space-y-2">
        ${buildAnswerRow(idx, 0, true)}
        ${buildAnswerRow(idx, 1, false)}
      </div>
    </div>
  `;
}

// ── Ligne de réponse vide ────────────────────────────────────────
function buildAnswerRow(qIdx, aIdx, isCorrect) {
  return `
    <div class="flex items-center gap-2 answer-row"
         id="answer-row-${qIdx}-${aIdx}"
         data-aidx="${aIdx}">
      <input type="radio"
             name="correct_radio_${qIdx}"
             value="${aIdx}"
             class="shrink-0"
             title="Marquer comme bonne réponse"
             ${isCorrect ? 'checked' : ''}
             onchange="setCorrectIndex(${qIdx}, ${aIdx})">

      <input type="text"
             name="questions[${qIdx}][answers][${aIdx}][answer]"
             required
             class="flex-1 border rounded-md px-3 py-1.5 text-sm"
             placeholder="Réponse ${aIdx + 1}">

      <input type="hidden"
             name="questions[${qIdx}][answers][${aIdx}][fraction]"
             id="fraction-${qIdx}-${aIdx}"
             value="${isCorrect ? '1' : '0'}">

      <button type="button" onclick="removeAnswer(this, ${qIdx})"
              class="text-red-400 hover:text-red-600 text-xs shrink-0">✕</button>
    </div>
  `;
}

// ── Utilitaires ──────────────────────────────────────────────────
function setCorrectIndex(qIdx, correctAIdx) {
  const ciField = document.getElementById(`correct-index-${qIdx}`);
  if (ciField) ciField.value = correctAIdx;

  const container = document.getElementById(`answers-${qIdx}`);
  container.querySelectorAll('.answer-row').forEach(row => {
    const aIdx   = parseInt(row.dataset.aidx);
    const hidden = document.getElementById(`fraction-${qIdx}-${aIdx}`);
    if (hidden) hidden.value = (aIdx === correctAIdx) ? '1' : '0';
  });
}

function addAnswer(qIdx) {
  const container = document.getElementById(`answers-${qIdx}`);
  const aIdx = answerCounts[qIdx]++;
  const div = document.createElement('div');
  div.innerHTML = buildAnswerRow(qIdx, aIdx, false);
  container.appendChild(div.firstElementChild);
}

function removeAnswer(btn, qIdx) {
  const row = btn.closest('.answer-row');
  const fractionHidden = document.getElementById(`fraction-${qIdx}-${row.dataset.aidx}`);
  const wasCorrect = fractionHidden && fractionHidden.value === '1';
  row.remove();

  if (wasCorrect) {
    const container = document.getElementById(`answers-${qIdx}`);
    const firstRow  = container.querySelector('.answer-row');
    if (firstRow) {
      const firstAIdx = parseInt(firstRow.dataset.aidx);
      const radio = firstRow.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
      setCorrectIndex(qIdx, firstAIdx);
    }
  }
}

function removeQuestion(btn) {
  btn.closest('.question-block').remove();
  if (!document.getElementById('questions-container').children.length) {
    document.getElementById('no-questions-msg').style.display = '';
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
</script>
@endsection