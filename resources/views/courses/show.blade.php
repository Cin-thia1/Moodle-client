@extends('layouts.app')

@section('title', $course->fullname)

@section('content')

{{-- Conteneur principal du cours --}}
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- =================================================== -->
        <!-- == CONTENU PRINCIPAL DU COURS                      == -->
        <!-- =================================================== -->
        <main>
                <header class="mb-10">
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">{{ $course->fullname }}</h1>
                    <p class="mt-2 text-lg text-gray-600">Animé par <span class="font-semibold text-indigo-600">{{ $course->teacher->username ?? 'admin' }}</span></p>
                </header>

                <!-- Navigation par onglets - Sections principales pour les étudiants -->
                <section id="student-sections" data-section="student-sections" class="scroll-mt-24 mb-12">
                    <div class="bg-white rounded-lg shadow-lg border-b">
                        <nav class="flex border-b overflow-x-auto">
                            <button onclick="switchStudentTab('overview')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-indigo-600 text-indigo-600 font-semibold" data-tab="overview">
                                <i class="fas fa-info-circle"></i> Aperçu
                            </button>
                            <button onclick="switchStudentTab('announcements')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="announcements">
                                <i class="fas fa-bullhorn"></i> Annonces
                            </button>
                            <button onclick="switchStudentTab('documents')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="documents">
                                <i class="fas fa-file"></i> Documents
                            </button>
                            <button onclick="switchStudentTab('participants')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="participants">
                                <i class="fas fa-users"></i> Participants
                            </button>
                            <button onclick="switchStudentTab('grades')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="grades">
                                <i class="fas fa-chart-line"></i> Mes notes
                            </button>
                            <button onclick="switchStudentTab('competencies')" class="student-tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="competencies">
                                <i class="fas fa-trophy"></i> Compétences
                            </button>
                        </nav>
                    </div>

                    <div class="mt-6">
                        <!-- Overview Tab -->
                        <div id="overview" class="student-tab-content">
                            <div class="bg-white rounded-lg shadow-lg p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">Détails du cours</h2>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                    <div class="flex flex-col"><dt class="text-sm font-medium text-gray-500">Nom court</dt><dd class="text-lg text-gray-900 font-semibold">{{ $course->shortname }}</dd></div>
                                    <div class="flex flex-col"><dt class="text-sm font-medium text-gray-500">Sections</dt><dd class="text-lg text-gray-900 font-semibold">{{ $course->numsections }}</dd></div>
                                    <div class="flex flex-col"><dt class="text-sm font-medium text-gray-500">Début</dt><dd class="text-lg text-gray-900 font-semibold">{{ $course->startdate ? $course->startdate->format('d F Y') : 'Non définie' }}</dd></div>
                                    <div class="flex flex-col"><dt class="text-sm font-medium text-gray-500">Fin</dt><dd class="text-lg text-gray-900 font-semibold">{{ $course->enddate ? $course->enddate->format('d F Y') : 'Non définie' }}</dd></div>
                                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Description</dt><dd class="mt-1 text-gray-700 leading-relaxed">{{ $course->summary }}</dd></div>
                                </dl>
                            </div>
                        </div>

                        <!-- Annonces Tab -->
                        <div id="announcements" class="student-tab-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">📢 Annonces</h2>
                                <a href="{{ route('announcements.index', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Voir tout →
                                </a>
                            </div>
                            <div class="space-y-4">
                                @forelse($course->announcements()->latest('published_at')->limit(3)->get() as $announcement)
                                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $announcement->subject }}</h3>
                                            <p class="text-sm text-gray-600">{{ $announcement->published_at?->format('d/m/Y H:i') ?? 'Non publié' }}</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ Str::limit($announcement->message, 200) }}</p>
                                </div>
                                @empty
                                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                                    <i class="fas fa-bullhorn text-4xl text-gray-300 mb-4 block"></i>
                                    <p class="text-gray-500">Aucune annonce pour le moment</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div id="documents" class="student-tab-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">📄 Documents</h2>
                                <a href="{{ route('documents.index', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Voir tout →
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @forelse($course->documents()->limit(6)->get() as $document)
                                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                                    <div class="flex items-center gap-3 mb-4">
                                        <i class="fas fa-file text-2xl text-green-600"></i>
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $document->filename }}</h3>
                                            <p class="text-xs text-gray-500">{{ number_format($document->filesize / 1024, 2) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('documents.download', [$course, $document]) }}" class="text-green-600 hover:text-green-800 text-sm font-semibold">
                                        <i class="fas fa-download"></i> Télécharger
                                    </a>
                                </div>
                                @empty
                                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                                    <i class="fas fa-file text-4xl text-gray-300 mb-4 block"></i>
                                    <p class="text-gray-500">Aucun document disponible</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Participants Tab -->
                        <div id="participants" class="student-tab-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">👥 Participants</h2>
                                <a href="{{ route('participants.index', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Voir tout →
                                </a>
                            </div>
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Utilisateur</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rôle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->participants()->with('user')->limit(5)->get() as $participant)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $participant->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->role === 'ROLE_TEACHER' ? 'bg-purple-100 text-purple-800' : ($participant->role === 'ROLE_STUDENT' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $participant->role === 'ROLE_TEACHER' ? 'Enseignant' : ($participant->role === 'ROLE_STUDENT' ? 'Étudiant' : 'Invité') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                                Aucun participant
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Grades Tab -->
                        <div id="grades" class="student-tab-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">📊 Mes notes</h2>
                                <a href="{{ route('grades.user', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Détails complets →
                                </a>
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                                <i class="fas fa-chart-line text-4xl text-gray-300 mb-4 block"></i>
                                <p class="text-gray-600 mb-4">Consultez vos notes complètes en cliquant sur le lien ci-dessus</p>
                                <a href="{{ route('grades.user', $course) }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                    Voir mes notes
                                </a>
                            </div>
                        </div>

                        <!-- Competencies Tab -->
                        <div id="competencies" class="student-tab-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">⭐ Compétences</h2>
                                <a href="{{ route('competencies.userCompetencies', auth()->id()) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                    Voir détails →
                                </a>
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                                <i class="fas fa-trophy text-4xl text-gray-300 mb-4 block"></i>
                                <p class="text-gray-600 mb-4">Consultez vos compétences et votre progression</p>
                                <a href="{{ route('competencies.userCompetencies', auth()->id()) }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                    Voir mes compétences
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="space-y-6">
                    @foreach ($course->sections as $section)
                        <section id="section-{{ $section->id }}" data-section="section-{{ $section->id }}" class="info-panel-container scroll-mt-24 bg-white rounded-2xl shadow-sm border overflow-hidden">
                            <h2 class="flex items-center justify-between px-6 py-4 cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors info-panel-toggle">
                                <span class="text-xl font-bold text-gray-800">{{ $section->name }}</span>
                                <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 info-panel-icon"></i>
                            </h2>
                            <div class="p-6 space-y-6 info-panel-content hidden">
                                @forelse ($section->modules as $module)
                                    @if ($module->modname == 'resource')
                                        {{-- Partiel: module-resource --}}
                                        <div class="bg-gray-50 rounded-lg p-5 border-l-4 border-blue-500 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <i class="fas fa-file-alt text-3xl text-blue-500"></i>
                                                <div><h3 class="font-semibold text-gray-800">{{ $module->name }}</h3><p class="text-sm text-gray-500">Ressource</p></div>
                                            </div>
                                            <a href="{{ route('module.download', $module->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm"><i class="fas fa-download"></i><span>Télécharger</span></a>
                                        </div>
                                    @elseif ($module->modname == 'assign')
                                        {{-- Partiel: module-assignment --}}
                                        <div class="bg-yellow-50 rounded-lg p-5 border-l-4 border-yellow-500 space-y-6">
                                            <header class="flex items-start gap-4"><i class="fas fa-tasks text-3xl text-yellow-600"></i><div><h3 class="text-lg font-bold text-gray-800">{{ $module->name }}</h3><p class="text-sm text-gray-500">Devoir à rendre</p></div></header>
                                            <div class="space-y-4 pl-10">
                                                @if($module->intro)<div class="prose prose-sm max-w-none text-gray-700">{!! $module->intro !!}</div>@endif
                                                @if($module->activity)<details class="bg-white p-4 rounded-lg border"><summary class="font-semibold cursor-pointer text-gray-800">Afficher les consignes</summary><div class="prose prose-sm max-w-none text-gray-700 mt-2">{!! $module->activity !!}</div></details>@endif
                                                @if ($module->pdf_url)<a href="{{ $module->pdf_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-red-700 font-medium hover:underline"><i class="fas fa-file-pdf text-red-500"></i><span>{{ $module->pdf_filename ?? 'Consignes en PDF' }}</span></a>@endif
                                            </div>
                                            <footer class="flex flex-wrap items-center justify-end gap-4 pt-4 border-t border-yellow-200">
                                                @if(Auth::user()->hasRole('ROLE_TEACHER'))
                                                    <a href="{{ route('assignments.submissions', $module->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 shadow-sm"><i class="fas fa-clipboard-check"></i><span>Corriger</span></a>
                                                @else
                                                    {{-- LE BOUTON CORRIGÉ AVEC LA MÉTHODE SIMPLE --}}
                                                    <button onclick="openSubmissionModal('{{ $module->id }}')" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 shadow-sm"><i class="fas fa-paper-plane"></i><span>Faire une soumission</span></button>
                                                @endif
                                            </footer>
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-center py-8 text-gray-500"><i class="fas fa-folder-open text-3xl mb-2"></i><p>Aucun module dans cette section.</p></div>
                                @endforelse

                                @if(Auth::user()->hasRole('ROLE_TEACHER'))
                                    {{-- Partiel: module-create-form --}}
                                    <div class="mt-6 pt-6 border-t border-dashed border-gray-300">
                                        {{-- ... (Le formulaire pour créer un module peut être ajouté ici si besoin) ... --}}
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            </main>
        </div>
    </div>
</div>

<!-- =================================================== -->
<!-- == MODAL DE SOUMISSION (HORS DU FLUX PRINCIPAL)    == -->
<!-- =================================================== -->
<div id="submissionModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6"><h3 class="text-2xl font-bold text-gray-800">Soumettre votre travail</h3><button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times fa-lg"></i></button></div>
        <form id="submissionForm">
            <input type="hidden" id="moduleId" name="module_id" value="">
            <div class="mb-5"><label for="responseText" class="block font-semibold text-gray-700 mb-2">Votre réponse</label><textarea id="responseText" name="response" rows="5" class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500" placeholder="Écrivez votre réponse ici..."></textarea></div>
            <div class="mb-8"><label class="block font-semibold text-gray-700 mb-2">Joindre un fichier</label><div class="relative flex items-center justify-center w-full h-32 border-2 border-dashed rounded-lg bg-gray-50"><input type="file" name="submission_file" id="submissionFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"><div class="text-center pointer-events-none"><i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i><p class="mt-2 text-sm text-gray-600"><span class="font-semibold text-indigo-600">Cliquez pour téléverser</span></p><p id="fileName" class="text-xs text-gray-500">Aucun fichier sélectionné</p></div></div></div>
            <div class="flex justify-end space-x-4"><button type="button" onclick="closeSubmissionModal()" class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300">Annuler</button><button type="button" onclick="submitAssignment()" class="px-6 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 shadow-sm">Valider</button></div>
        </form>
    </div>
</div>

<script>
// ============================================================
// == LOGIQUE JAVASCRIPT SIMPLE POUR LES ACCORDÉONS ET LE MODAL ==
// == (Comme dans votre version qui fonctionnait)             ==
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des accordéons (sections du cours)
    document.querySelectorAll('.info-panel-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const content = this.closest('.info-panel-container').querySelector('.info-panel-content');
            const icon = this.querySelector('.info-panel-icon');
            content.classList.toggle('hidden');
            icon.classList.toggle('-rotate-180'); // Utilise la rotation de Tailwind
        });
    });

    // Afficher le nom du fichier sélectionné dans le modal
    const submissionFileInput = document.getElementById('submissionFile');
    if (submissionFileInput) {
        submissionFileInput.addEventListener('change', function(e) {
            const fileNameSpan = document.getElementById('fileName');
            fileNameSpan.textContent = e.target.files.length ? e.target.files[0].name : 'Aucun fichier sélectionné';
        });
    }
});

// Fonctions globales pour le modal
function openSubmissionModal(moduleId) {
    document.getElementById('moduleId').value = moduleId;
    document.getElementById('submissionModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden'); // Empêche le défilement de l'arrière-plan
}

function closeSubmissionModal() {
    const modal = document.getElementById('submissionModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Réinitialiser le formulaire pour la prochaine fois
    document.getElementById('submissionForm').reset();
    document.getElementById('fileName').textContent = 'Aucun fichier sélectionné';
}

function submitAssignment() {
    // Ici, vous ajouteriez la logique de soumission AJAX avec fetch()
    console.log("Soumission du devoir pour le module ID: " + document.getElementById('moduleId').value);

    // Fermer le modal après la tentative de soumission
    closeSubmissionModal();

    // Afficher une notification (vous pouvez améliorer ce système)
    const notification = document.createElement('div');
    notification.className = 'fixed top-5 right-5 bg-green-500 text-white px-5 py-3 rounded-lg shadow-xl animate-fade-in-down';
    notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i> Votre travail a bien été soumis !`;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s ease';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
</script>

<script>
function switchStudentTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.student-tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.student-tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-600', 'hover:text-indigo-600');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.remove('hidden');
    
    // Mark button as active
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600', 'hover:text-indigo-600');
    activeBtn.classList.add('border-indigo-600', 'text-indigo-600');
}
</script>

@endsection
