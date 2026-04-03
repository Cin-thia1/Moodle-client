@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="p-6 border-b bg-gray-50">
      <div class="flex items-center gap-3">
        <a href="{{ route('assignments.index', ['course_id' => $selectedCourseId]) }}"
           class="text-gray-500 hover:text-gray-800 text-sm transition">← Retour</a>
        <span class="text-gray-300">|</span>
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Créer un Quiz</h2>
          <p class="text-sm text-gray-600 mt-1">QCM et Vrai/Faux avec correction automatique.</p>
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

      <form action="{{ route('quiz.store') }}" method="POST" class="space-y-8" id="quiz-form">
        @csrf

        {{-- ═══ Informations générales ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">
            Informations générales
          </h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
            <select name="course_id" class="w-full border rounded-lg px-3 py-2 text-sm"
                    onchange="window.location='{{ route('quiz.create') }}?course_id='+this.value">
              @foreach($courses as $course)
                <option value="{{ $course->id }}"
                  {{ (int)old('course_id', $selectedCourseId) === (int)$course->id ? 'selected' : '' }}>
                  {{ $course->fullname }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
            <select name="section_id" class="w-full border rounded-lg px-3 py-2 text-sm">
              @forelse($sections as $sec)
                <option value="{{ $sec->id }}" {{ (int)old('section_id') === (int)$sec->id ? 'selected' : '' }}>
                  {{ $sec->name }}
                </option>
              @empty
                <option value="">Aucune section disponible</option>
              @endforelse
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du quiz <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   placeholder="Ex : Quiz 1 – Algorithmique">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
            <textarea name="intro" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"
                      placeholder="Instructions pour les étudiants…">{{ old('intro') }}</textarea>
          </div>
        </div>

        {{-- ═══ Chronométrage ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">Chronométrage</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ouverture du quiz</label>
              <input type="datetime-local" name="timeopen" value="{{ old('timeopen') }}"
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fermeture du quiz</label>
              <input type="datetime-local" name="timeclose" value="{{ old('timeclose') }}"
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
          </div>

          <div class="md:w-1/2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Durée limite (minutes) <span class="text-gray-400 text-xs">– laisser vide = illimité</span>
            </label>
            <input type="number" name="timelimit" value="{{ old('timelimit') }}" min="1"
                   class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ex : 60">
          </div>
        </div>

        {{-- ═══ Notes ═══ --}}
        <div class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">Notes</h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Note maximale <span class="text-red-500">*</span></label>
              <input type="number" name="grade" value="{{ old('grade', 20) }}" min="0" max="100" required
                     class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tentatives autorisées</label>
              <select name="attempts" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="1" {{ old('attempts', 1) == 1 ? 'selected' : '' }}>1 tentative</option>
                <option value="2" {{ old('attempts') == 2 ? 'selected' : '' }}>2 tentatives</option>
                <option value="3" {{ old('attempts') == 3 ? 'selected' : '' }}>3 tentatives</option>
                <option value="0" {{ old('attempts') == 0 ? 'selected' : '' }}>Illimité</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de notation</label>
              <select name="grademethod" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="0" {{ old('grademethod', 0) == 0 ? 'selected' : '' }}>Note la plus haute</option>
                <option value="1" {{ old('grademethod') == 1 ? 'selected' : '' }}>Moyenne</option>
                <option value="2" {{ old('grademethod') == 2 ? 'selected' : '' }}>Première tentative</option>
                <option value="3" {{ old('grademethod') == 3 ? 'selected' : '' }}>Dernière tentative</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="shuffleanswers" id="shuffleanswers" value="1"
                   class="rounded" {{ old('shuffleanswers', 1) ? 'checked' : '' }}>
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

          <div id="no-questions-msg" class="text-sm text-gray-400 text-center py-6 bg-gray-50 rounded-lg">
            Aucune question. Cliquez sur "+ QCM" ou "+ Vrai/Faux" pour commencer.
          </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t">
          <a href="{{ route('assignments.index', ['course_id' => $selectedCourseId]) }}"
             class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
            Annuler
          </a>
          <button type="submit"
                  class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
            Créer le quiz
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
let questionCount = 0;
// Compteur de réponses par question (qIdx → nombre)
const answerCounts = {};

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

// ── Vrai / Faux ─────────────────────────────────────────────────
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

// ── QCM ─────────────────────────────────────────────────────────
// ✅ Technique corrigée : un champ hidden "correct_index" par question
// indique l'INDEX de la bonne réponse. Le controller lit cet index
// pour assigner fraction=1 à la bonne réponse et fraction=0 aux autres.
function buildMultichoice(idx) {
  answerCounts[idx] = 2; // on démarre avec 2 réponses
  return `
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-medium text-gray-600">
          Réponses — cliquez le bouton radio pour marquer la bonne réponse
        </label>
        <button type="button" onclick="addAnswer(${idx})"
                class="text-xs text-blue-600 hover:underline">+ Ajouter une réponse</button>
      </div>

      {{-- ✅ Checkboxes : sélectionner plusieurs bonnes réponses --}}
      <div id="answers-${idx}" class="space-y-2">
        ${buildAnswerRow(idx, 0, false)}
        ${buildAnswerRow(idx, 1, false)}
      </div>
    </div>
  `;
}

function buildAnswerRow(qIdx, aIdx, isCorrect) {
  return `
    <div class="flex items-center gap-2 answer-row"
         id="answer-row-${qIdx}-${aIdx}"
         data-aidx="${aIdx}">

      {{-- Checkbox : marquer comme bonne réponse (possibilité multiple) --}}
      <input type="checkbox"
             class="shrink-0 correct-answer-checkbox"
             title="Marquer comme bonne réponse"
             ${isCorrect ? 'checked' : ''}
             onchange="updateCorrectAnswers(${qIdx})">

      <input type="text"
             name="questions[${qIdx}][answers][${aIdx}][answer]"
             required
             class="flex-1 border rounded-md px-3 py-1.5 text-sm"
             placeholder="Réponse ${aIdx + 1}">

      {{-- fraction envoyée au serveur (mis à jour par updateCorrectAnswers) --}}
      <input type="hidden"
             name="questions[${qIdx}][answers][${aIdx}][fraction]"
             id="fraction-${qIdx}-${aIdx}"
             value="${isCorrect ? '1' : '0'}">

      <button type="button" onclick="removeAnswer(this, ${qIdx})"
              class="text-red-400 hover:text-red-600 text-xs shrink-0">✕</button>
    </div>
  `;
}

// ✅ Met à jour les fractions selon les checkboxes cochées (plusieurs possibles)
function updateCorrectAnswers(qIdx) {
  const container = document.getElementById(`answers-${qIdx}`);
  container.querySelectorAll('.answer-row').forEach(row => {
    const aIdx   = parseInt(row.dataset.aidx);
    const checkbox = row.querySelector('.correct-answer-checkbox');
    const hidden = document.getElementById(`fraction-${qIdx}-${aIdx}`);
    if (hidden && checkbox) {
      hidden.value = checkbox.checked ? '1' : '0';
    }
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
  btn.closest('.answer-row').remove();
}

function removeQuestion(btn) {
  btn.closest('.question-block').remove();
  if (!document.getElementById('questions-container').children.length) {
    document.getElementById('no-questions-msg').style.display = '';
  }
}
</script>
@endsection