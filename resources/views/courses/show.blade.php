@extends('layouts.app')

@section('title', $course->fullname)

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <main>
            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-chevron-left"></i> Retour
                </a>
            </div>

            <!-- Hero Header with Image -->
            @if($course->image && \Storage::disk('public')->exists($course->image))
            <div class="mb-8 rounded-2xl overflow-hidden shadow-xl">
                <div class="relative h-72 w-full">
                    <img src="{{ \App\Helpers\ImageHelper::getCourseImageUrl($course->image) }}" alt="{{ $course->fullname }}" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <h1 class="text-5xl font-black mb-2 drop-shadow-lg">{{ $course->fullname }}</h1>
                        <p class="text-lg font-medium drop-shadow-md">Instructeur: <span class="text-indigo-300">{{ $course->teacher->name ?? $course->teacher->username ?? 'Admin' }}</span></p>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-8 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-xl p-12 text-white">
                <h1 class="text-5xl font-black mb-2">{{ $course->fullname }}</h1>
                <p class="text-lg font-medium text-indigo-100">Instructeur: {{ $course->teacher->name ?? $course->teacher->username ?? 'Admin' }}</p>
            </div>
            @endif

            <!-- Success/Error Messages -->
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-1 flex-shrink-0"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-red-900 mb-2">Erreurs de validation</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                <div>
                    <h3 class="font-semibold text-green-900">{{ session('success') }}</h3>
                </div>
            </div>
            @endif

            <!-- Tab Navigation & Content -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 overflow-x-auto">
                    <nav class="flex">
                        <button onclick="switchTab('overview')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-indigo-600 text-indigo-600 font-semibold hover:bg-indigo-50 transition-colors whitespace-nowrap" data-tab="overview">
                            <i class="fas fa-info-circle"></i> <span class="hidden sm:inline">Aperçu</span>
                        </button>
                        <button onclick="switchTab('sections')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="sections">
                            <i class="fas fa-book"></i> <span class="hidden sm:inline">Sections</span>
                        </button>
                        <button onclick="switchTab('announcements')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="announcements">
                            <i class="fas fa-bullhorn"></i> <span class="hidden sm:inline">Annonces</span>
                        </button>
                        <button onclick="switchTab('documents')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="documents">
                            <i class="fas fa-file"></i> <span class="hidden sm:inline">Documents</span>
                        </button>
                        <button onclick="switchTab('participants')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="participants">
                            <i class="fas fa-users"></i> <span class="hidden sm:inline">Participants</span>
                        </button>
                        <button onclick="switchTab('competencies')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="competencies">
                            <i class="fas fa-star"></i> <span class="hidden sm:inline">Compétences</span>
                        </button>
                        @if(Auth::user() && Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER']))
                        <button onclick="switchTab('settings')" class="tab-btn flex items-center justify-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 font-semibold hover:text-indigo-600 hover:bg-gray-50 transition-colors whitespace-nowrap" data-tab="settings">
                            <i class="fas fa-cog"></i> <span class="hidden sm:inline">Paramètres</span>
                        </button>
                        @endif
                    </nav>
                </div>

                <div class="p-8">
                    <!-- Tab: Overview -->
                    <div id="overview" class="tab-content">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-info-circle text-indigo-600"></i> Détails du cours
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border-l-4 border-blue-500">
                                <div class="text-sm font-medium text-blue-600 mb-1">Sections</div>
                                <div class="text-3xl font-bold text-blue-900">{{ $course->sections->count() }}</div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border-l-4 border-green-500">
                                <div class="text-sm font-medium text-green-600 mb-1">Participants</div>
                                <div class="text-3xl font-bold text-green-900">{{ $course->participants()->count() ?? 0 }}</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border-l-4 border-purple-500">
                                <div class="text-sm font-medium text-purple-600 mb-1">Annonces</div>
                                <div class="text-3xl font-bold text-purple-900">{{ $course->announcements()->count() ?? 0 }}</div>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border-l-4 border-orange-500">
                                <div class="text-sm font-medium text-orange-600 mb-1">Compétences</div>
                                <div class="text-3xl font-bold text-orange-900">{{ $course->competencies->count() }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Informations générales -->
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-id-card text-indigo-600"></i> Informations générales
                                </h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Nom complet</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $course->fullname }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Nom court</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $course->shortname }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">N° d'identification</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $course->idnumber ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Catégorie</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $course->category->name ?? '—' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Dates & Visibilité -->
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-indigo-600"></i> Dates & Visibilité
                                </h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Date de début</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">
                                            @if($course->startdate)
                                                <i class="fas fa-check-circle text-green-600 mr-2"></i>{{ $course->startdate->format('d/m/Y - H:i') }}
                                            @else
                                                <span class="text-gray-500">Non définie</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Date de fin</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">
                                            @if($course->enddate)
                                                <i class="fas fa-check-circle text-green-600 mr-2"></i>{{ $course->enddate->format('d/m/Y - H:i') }}
                                            @else
                                                <span class="text-gray-500">Non définie</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Visibilité</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium {{ $course->visible ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                                <i class="fas {{ $course->visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                {{ $course->visible ? 'Visible' : 'Masqué' }}
                                            </span>
                                        </dd>
                                    </div>
                                    @if($course->tags)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Tags</dt>
                                        <dd class="mt-1 flex flex-wrap gap-2">
                                            @foreach(explode(',', $course->tags) as $tag)
                                                <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">{{ trim($tag) }}</span>
                                            @endforeach
                                        </dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>

                            <!-- Format & Paramètres -->
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-cogs text-indigo-600"></i> Paramètres
                                </h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Format du cours</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">
                                            @switch($course->format)
                                                @case('topics') Thématique @break
                                                @case('weeks') Hebdomadaire @break
                                                @case('social') Informel @break
                                                @case('singleactivity') Activité unique @break
                                                @default {{ $course->format ?? 'Thématique' }}
                                            @endswitch
                                            ({{ $course->numsections }} sections)
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Langue imposée</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $course->lang ? strtoupper($course->lang) : 'Non imposée' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Mode de groupe</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">
                                            @switch($course->groupmode)
                                                @case(1) Groupes visibles @break
                                                @case(2) Groupes séparés @break
                                                @default Pas de groupe
                                            @endswitch
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600">Taille maximale des fichiers</dt>
                                        <dd class="mt-1 text-base text-gray-900 font-semibold">
                                            @if($course->maxbytes == 0) Limite du site
                                            @elseif($course->maxbytes >= 1048576) {{ round($course->maxbytes / 1048576) }} Mo
                                            @else {{ $course->maxbytes }} octets
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Description -->
                            <div class="lg:col-span-3 bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                        <i class="fas fa-file-alt text-indigo-600"></i> Description
                                    </h3>
                                    @if(auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER']))
                                    <button onclick="switchTab('settings')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Modifier les paramètres
                                    </button>
                                    @endif
                                </div>
                                <div class="flex flex-col md:flex-row gap-6">
                                    @if($course->image)
                                    <div class="flex-shrink-0">
                                        <img src="{{ Storage::url($course->image) }}" alt="Image du cours" class="w-64 h-40 object-cover rounded-lg shadow-sm">
                                    </div>
                                    @endif
                                    <div class="flex-1 text-gray-700 leading-relaxed whitespace-pre-line">
                                        @if($course->summary)
                                            {{ $course->summary }}
                                        @else
                                            <span class="text-gray-500 italic">Aucune description disponible</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Sections -->
                    <div id="sections" class="tab-content hidden">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-book text-indigo-600"></i> Sections du cours
                        </h2>
                        @if($course->sections->isEmpty())
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-inbox text-gray-400 text-5xl mb-4 block"></i>
                                <p class="text-gray-600 text-lg">Aucune section disponible pour le moment.</p>
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach ($course->sections as $section)
                                <section class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                                    <button onclick="toggleSection(this)" class="w-full flex items-center justify-between px-6 py-5 cursor-pointer bg-gradient-to-r from-gray-50 to-transparent hover:from-gray-100 transition-colors">
                                        <span class="text-xl font-semibold text-gray-900">{{ $section->name }}</span>
                                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                                    </button>
                                    <div class="hidden px-6 py-5 space-y-4 border-t border-gray-100 bg-gray-50">
                                        @forelse ($section->modules as $module)
                                            @if ($module->modname == 'resource')
                                                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 flex items-center justify-between gap-4">
                                                    <div class="flex items-center gap-4 flex-1">
                                                        <i class="fas fa-file-alt text-2xl text-blue-500 flex-shrink-0"></i>
                                                        <div>
                                                            <h4 class="font-semibold text-gray-900">{{ $module->name }}</h4>
                                                            <p class="text-xs text-gray-500">Ressource</p>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('modules.download', $module->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0">
                                                        <i class="fas fa-download"></i> <span class="hidden sm:inline">Télécharger</span>
                                                    </a>
                                                </div>
                                            @elseif ($module->modname == 'assign')
                                                <div class="bg-white rounded-lg border-l-4 border-yellow-500 p-5 space-y-4">
                                                    <div class="flex items-start gap-4">
                                                        <i class="fas fa-tasks text-2xl text-yellow-600 flex-shrink-0 mt-1"></i>
                                                        <div class="flex-1">
                                                            <h4 class="text-lg font-semibold text-gray-900">{{ $module->name }}</h4>
                                                            <p class="text-sm text-gray-600">Devoir à rendre</p>
                                                        </div>
                                                    </div>
                                                    @if($module->intro)
                                                        <div class="text-gray-700">{{ Str::limit(strip_tags($module->intro), 200) }}</div>
                                                    @endif
                                                    <div class="flex flex-wrap gap-3 pt-4">
                                                        @if(Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_MANAGER']))
                                                            <a href="{{ route('assignments.submissions', $module->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                                <i class="fas fa-clipboard-check"></i> Corriger
                                                            </a>
                                                        @else
                                                            <button onclick="openSubmissionModal('{{ $module->id }}')" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                                                <i class="fas fa-paper-plane"></i> Soumettre
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <div class="text-center py-8 text-gray-500">
                                                <i class="fas fa-folder-open text-3xl mb-2"></i>
                                                <p>Aucun module dans cette section.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </section>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Tab: Announcements -->
                    <div id="announcements" class="tab-content hidden">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-bullhorn text-indigo-600"></i> Annonces
                        </h2>
                        <div class="space-y-4">
                            @forelse($course->announcements()->latest('published_at')->limit(10)->get() as $announcement)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow p-6 border-l-4 border-indigo-500">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $announcement->subject }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-clock mr-1"></i>{{ $announcement->published_at?->format('d/m/Y à H:i') ?? 'Non publié' }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-gray-700">{{ Str::limit($announcement->message, 300) }}</p>
                            </div>
                            @empty
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-bullhorn text-gray-400 text-5xl mb-4 block"></i>
                                <p class="text-gray-600 text-lg">Aucune annonce pour le moment</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab: Documents -->
                    <div id="documents" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                                <i class="fas fa-file-alt text-indigo-600"></i> Documents
                            </h2>
                            @if(Auth::user() && Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER']))
                            <button onclick="openDocumentUploadModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-plus"></i> <span class="hidden sm:inline">Ajouter</span>
                            </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($course->documents as $document)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow p-6 border-l-4 border-green-500">
                                <div class="flex items-start justify-between mb-3">
                                    <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                    @if(Auth::user() && Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER']))
                                    <button onclick="deleteDocument('{{ $document->id }}')" class="text-gray-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 truncate">{{ $document->name }}</h3>
                                @if($document->description)
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $document->description }}</p>
                                @endif
                                <a href="{{ $document->file_url }}" target="_blank" class="mt-4 inline-flex items-center gap-2 px-3 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors w-full justify-center">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                            </div>
                            @empty
                            <div class="col-span-full text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-file-alt text-gray-400 text-5xl mb-4 block"></i>
                                <p class="text-gray-600 text-lg">Aucun document disponible</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab: Participants -->
                    <div id="participants" class="tab-content hidden">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-users text-indigo-600"></i> Participants
                        </h2>
                        <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Utilisateur</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Rôle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($course->participants()->with('user')->get() as $participant)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ $participant->user?->name ?? 'Utilisateur supprimé' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $participant->user?->email ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->role === 'ROLE_TEACHER' ? 'bg-purple-100 text-purple-800' : ($participant->role === 'ROLE_STUDENT' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $participant->role === 'ROLE_TEACHER' ? 'Enseignant' : ($participant->role === 'ROLE_STUDENT' ? 'Étudiant' : 'Invité') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                            <p>Aucun participant</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab: Competencies -->
                    <div id="competencies" class="tab-content hidden">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-star text-indigo-600"></i> Compétences du cours
                        </h2>
                        @if($course->competencies->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($course->competencies as $competency)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow p-6 border-l-4 border-orange-500">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $competency->shortname }}</h3>
                                            <p class="text-xs text-gray-500 mt-1">ID: {{ $competency->idnumber }}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                            <i class="fas fa-hourglass-half mr-1"></i> En cours
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $competency->description }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-star text-gray-400 text-5xl mb-4 block"></i>
                                <p class="text-gray-600 text-lg">Aucune compétence n'est associée à ce cours</p>
                            </div>
                        @endif
                    </div>

                    <!-- Tab: Settings (Parameters) -->
                    @if(Auth::user() && Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER']))
                    <div id="settings" class="tab-content hidden">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-cog text-indigo-600"></i> Paramètres du cours
                        </h2>
                        
                        <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            @method('PUT')
                            
                            <!-- General Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-id-card text-indigo-600"></i> Informations générales
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="fullname" class="block text-sm font-medium text-gray-700 mb-2">Nom complet du cours <span class="text-red-500">*</span></label>
                                        <input type="text" name="fullname" id="fullname" value="{{ old('fullname', $course->fullname) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="shortname" class="block text-sm font-medium text-gray-700 mb-2">Nom court du cours <span class="text-red-500">*</span></label>
                                        <input type="text" name="shortname" id="shortname" value="{{ old('shortname', $course->shortname) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="idnumber" class="block text-sm font-medium text-gray-700 mb-2">N° d'identification du cours</label>
                                        <input type="text" name="idnumber" id="idnumber" value="{{ old('idnumber', $course->idnumber) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie <span class="text-red-500">*</span></label>
                                        <select name="category_id" id="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            @foreach(\App\Models\Category::all() as $cat)
                                                <option value="{{ $cat->id }}" {{ $course->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="visible" class="block text-sm font-medium text-gray-700 mb-2">Visibilité du cours</label>
                                        <select name="visible" id="visible" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->visible == 1 ? 'selected' : '' }}>Visible</option>
                                            <option value="0" {{ $course->visible == 0 ? 'selected' : '' }}>Masqué</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-indigo-600"></i> Dates
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="startdate" class="block text-sm font-medium text-gray-700 mb-2">Date de début <span class="text-red-500">*</span></label>
                                        <input type="datetime-local" name="startdate" id="startdate" value="{{ old('startdate', $course->startdate ? $course->startdate->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="enddate" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                                        <input type="datetime-local" name="enddate" id="enddate" value="{{ old('enddate', $course->enddate ? $course->enddate->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            <!-- Description Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-file-alt text-indigo-600"></i> Description
                                </h3>
                                <div>
                                    <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">Résumé du cours</label>
                                    <textarea name="summary" id="summary" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Décrivez votre cours...">{{ old('summary', $course->summary) }}</textarea>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-image text-indigo-600"></i> Image du cours
                                </h3>
                                <div class="flex gap-6">
                                    <div class="flex-1">
                                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Choisir une image</label>
                                        <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onchange="previewImage(event)">
                                        <p class="text-sm text-gray-500 mt-2">Formats acceptés: JPEG, PNG, GIF (max. 2 Mo)</p>
                                    </div>
                                    @if($course->image && \Storage::disk('public')->exists($course->image))
                                    <div class="w-32 h-32 flex-shrink-0">
                                        <img id="imagePreview" src="{{ \App\Helpers\ImageHelper::getCourseImageUrl($course->image) }}" alt="Aperçu" class="w-full h-full object-cover rounded-lg border border-gray-300">
                                    </div>
                                    @else
                                    <div id="imagePreview" class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center hidden">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Format Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-book-open text-indigo-600"></i> Format du cours
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                                        <select name="format" id="format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="topics" {{ $course->format == 'topics' ? 'selected' : '' }}>Thématique</option>
                                            <option value="weeks" {{ $course->format == 'weeks' ? 'selected' : '' }}>Hebdomadaire</option>
                                            <option value="social" {{ $course->format == 'social' ? 'selected' : '' }}>Informel</option>
                                            <option value="singleactivity" {{ $course->format == 'singleactivity' ? 'selected' : '' }}>Activité unique</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="numsections" class="block text-sm font-medium text-gray-700 mb-2">Nombre de sections</label>
                                        <input type="number" name="numsections" id="numsections" value="{{ old('numsections', $course->numsections) }}" min="0" max="52" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="hiddensections" class="block text-sm font-medium text-gray-700 mb-2">Sections cachées</label>
                                        <select name="hiddensections" id="hiddensections" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="0" {{ $course->hiddensections == 0 ? 'selected' : '' }}>Invisibles</option>
                                            <option value="1" {{ $course->hiddensections == 1 ? 'selected' : '' }}>Condensées</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="coursedisplay" class="block text-sm font-medium text-gray-700 mb-2">Mise en page</label>
                                        <select name="coursedisplay" id="coursedisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="0" {{ $course->coursedisplay == 0 ? 'selected' : '' }}>Toutes les sections</option>
                                            <option value="1" {{ $course->coursedisplay == 1 ? 'selected' : '' }}>Une section par page</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Appearance Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-palette text-indigo-600"></i> Apparence
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="lang" class="block text-sm font-medium text-gray-700 mb-2">Langue</label>
                                        <select name="lang" id="lang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="">Ne pas imposer</option>
                                            <option value="fr" {{ $course->lang == 'fr' ? 'selected' : '' }}>Français</option>
                                            <option value="en" {{ $course->lang == 'en' ? 'selected' : '' }}>Anglais</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="newsitems" class="block text-sm font-medium text-gray-700 mb-2">Nombre d'annonces</label>
                                        <input type="number" name="newsitems" id="newsitems" value="{{ old('newsitems', $course->newsitems) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="showgrades" class="block text-sm font-medium text-gray-700 mb-2">Afficher le carnet de notes</label>
                                        <select name="showgrades" id="showgrades" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->showgrades == 1 ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ $course->showgrades == 0 ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="showreports" class="block text-sm font-medium text-gray-700 mb-2">Afficher les rapports</label>
                                        <select name="showreports" id="showreports" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->showreports == 1 ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ $course->showreports == 0 ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="showactivitydates" class="block text-sm font-medium text-gray-700 mb-2">Afficher les dates d'activité</label>
                                        <select name="showactivitydates" id="showactivitydates" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->showactivitydates == 1 ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ $course->showactivitydates == 0 ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-indigo-600"></i> Paramètres avancés
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="maxbytes" class="block text-sm font-medium text-gray-700 mb-2">Taille max. des fichiers</label>
                                        <select name="maxbytes" id="maxbytes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="0" {{ $course->maxbytes == 0 ? 'selected' : '' }}>Limite du site</option>
                                            <option value="104857600" {{ $course->maxbytes == 104857600 ? 'selected' : '' }}>100 Mo</option>
                                            <option value="52428800" {{ $course->maxbytes == 52428800 ? 'selected' : '' }}>50 Mo</option>
                                            <option value="10485760" {{ $course->maxbytes == 10485760 ? 'selected' : '' }}>10 Mo</option>
                                            <option value="2097152" {{ $course->maxbytes == 2097152 ? 'selected' : '' }}>2 Mo</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="enablecompletion" class="block text-sm font-medium text-gray-700 mb-2">Suivi d'achèvement</label>
                                        <select name="enablecompletion" id="enablecompletion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->enablecompletion == 1 ? 'selected' : '' }}>Activé</option>
                                            <option value="0" {{ $course->enablecompletion == 0 ? 'selected' : '' }}>Désactivé</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="showcompletionconditions" class="block text-sm font-medium text-gray-700 mb-2">Conditions d'achèvement</label>
                                        <select name="showcompletionconditions" id="showcompletionconditions" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->showcompletionconditions == 1 ? 'selected' : '' }}>Affichées</option>
                                            <option value="0" {{ $course->showcompletionconditions == 0 ? 'selected' : '' }}>Masquées</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="groupmode" class="block text-sm font-medium text-gray-700 mb-2">Mode de groupe</label>
                                        <select name="groupmode" id="groupmode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="0" {{ $course->groupmode == 0 ? 'selected' : '' }}>Pas de groupe</option>
                                            <option value="1" {{ $course->groupmode == 1 ? 'selected' : '' }}>Groupes visibles</option>
                                            <option value="2" {{ $course->groupmode == 2 ? 'selected' : '' }}>Groupes séparés</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="groupmodeforce" class="block text-sm font-medium text-gray-700 mb-2">Imposer le mode de groupe</label>
                                        <select name="groupmodeforce" id="groupmodeforce" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="1" {{ $course->groupmodeforce == 1 ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ $course->groupmodeforce == 0 ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="defaultgroupingid" class="block text-sm font-medium text-gray-700 mb-2">Groupement par défaut</label>
                                        <select name="defaultgroupingid" id="defaultgroupingid" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="0" {{ $course->defaultgroupingid == 0 ? 'selected' : '' }}>Aucun</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Étiquettes</label>
                                        <input type="text" name="tags" id="tags" value="{{ old('tags', $course->tags) }}" placeholder="Séparées par des virgules" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4 pt-6">
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                                <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
<div id="submissionModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Soumettre votre travail</h3>
            <button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="submissionForm">
            <input type="hidden" id="moduleId" name="module_id" value="">
            <div class="mb-5">
                <label for="responseText" class="block font-semibold text-gray-700 mb-2">Votre réponse</label>
                <textarea id="responseText" name="response" rows="5" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500" placeholder="Écrivez votre réponse ici..."></textarea>
            </div>
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-2">Joindre un fichier</label>
                <div class="relative flex items-center justify-center w-full h-32 border-2 border-dashed border-indigo-300 rounded-lg bg-indigo-50">
                    <input type="file" name="submission_file" id="submissionFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="text-center pointer-events-none">
                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-400 mb-2 block"></i>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-indigo-600">Cliquez pour téléverser</span></p>
                        <p id="fileName" class="text-xs text-gray-500 mt-1">Aucun fichier sélectionné</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeSubmissionModal()" class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">Annuler</button>
                <button type="button" onclick="submitAssignment()" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">Valider</button>
            </div>
        </form>
    </div>
</div>

<div id="documentUploadModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Ajouter un document</h3>
            <button onclick="closeDocumentUploadModal()" class="text-gray-400 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="documentUploadForm" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <div class="mb-6">
                <label for="documentFile" class="block font-semibold text-gray-700 mb-2">Sélectionner un fichier</label>
                <div class="relative flex items-center justify-center w-full h-32 border-2 border-dashed border-indigo-300 rounded-lg bg-indigo-50">
                    <input type="file" name="document_file" id="documentFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                    <div class="text-center pointer-events-none">
                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-400 mb-2 block"></i>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-indigo-600">Cliquez ou déposez</span></p>
                        <p id="uploadFileName" class="text-xs text-gray-500 mt-1">Aucun fichier sélectionné</p>
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <label for="documentDescription" class="block font-semibold text-gray-700 mb-2">Description (optionnel)</label>
                <textarea id="documentDescription" name="description" rows="3" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500" placeholder="Décrivez le document..."></textarea>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDocumentUploadModal()" class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">Annuler</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    document.getElementById(tabName).classList.remove('hidden');
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600');
    activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
    
    if (window.location.hash !== `#${tabName}`) {
        window.history.replaceState(null, '', `#${tabName}`);
    }
}

// Toggle section content
function toggleSection(btn) {
    const content = btn.nextElementSibling;
    const icon = btn.querySelector('i');
    content.classList.toggle('hidden');
    icon.classList.toggle('-rotate-180');
}

// Modal functions
function openSubmissionModal(moduleId) {
    document.getElementById('moduleId').value = moduleId;
    document.getElementById('submissionModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeSubmissionModal() {
    const modal = document.getElementById('submissionModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('submissionForm').reset();
    document.getElementById('fileName').textContent = 'Aucun fichier sélectionné';
}

function openDocumentUploadModal() {
    document.getElementById('documentUploadModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDocumentUploadModal() {
    const modal = document.getElementById('documentUploadModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('documentUploadForm').reset();
    document.getElementById('uploadFileName').textContent = 'Aucun fichier sélectionné';
}

// File upload handlers
document.addEventListener('DOMContentLoaded', function() {
    const submissionFileInput = document.getElementById('submissionFile');
    if (submissionFileInput) {
        submissionFileInput.addEventListener('change', function(e) {
            const fileNameSpan = document.getElementById('fileName');
            fileNameSpan.textContent = e.target.files.length ? e.target.files[0].name : 'Aucun fichier sélectionné';
        });
    }

    const documentFileInput = document.getElementById('documentFile');
    if (documentFileInput) {
        documentFileInput.addEventListener('change', function(e) {
            const fileNameSpan = document.getElementById('uploadFileName');
            fileNameSpan.textContent = e.target.files.length ? e.target.files[0].name : 'Aucun fichier sélectionné';
        });
    }

    const documentUploadForm = document.getElementById('documentUploadForm');
    if (documentUploadForm) {
        documentUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const courseId = formData.get('course_id');
            
            fetch(`/api/courses/${courseId}/documents`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDocumentUploadModal();
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible d\'ajouter le document'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur s\'est produite lors de l\'upload');
            });
        });
    }

    if (window.location.hash) {
        const tabName = window.location.hash.substring(1);
        if (document.querySelector(`[data-tab="${tabName}"]`)) {
            switchTab(tabName);
        }
    }
});

function submitAssignment() {
    const moduleId = document.getElementById('moduleId').value;
    const form = document.getElementById('submissionForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/assignments/${moduleId}/submit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.id) {
            closeSubmissionModal();
            const notification = document.createElement('div');
            notification.className = 'fixed top-5 right-5 bg-green-500 text-white px-5 py-3 rounded-lg shadow-xl';
            notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i> Votre travail a bien été soumis !`;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    notification.remove();
                    location.reload();
                }, 500);
            }, 2000);
        } else {
            alert('Erreur: ' + (data.message || 'Impossible de soumettre le devoir.'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la soumission.');
    });
}

function deleteDocument(documentId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
        return;
    }
    
    fetch(`/api/documents/${documentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + (data.message || 'Impossible de supprimer le document'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite');
    });
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        if (preview) {
            preview.src = reader.result;
            preview.classList.remove('hidden');
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection
