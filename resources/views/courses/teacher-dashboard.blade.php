@extends('layouts.app')

@section('title', 'Gestion - ' . $course->fullname)

@section('content')
<div class="bg-gray-50 min-h-screen">


    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('courses.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left"></i> Mes cours
            </a>
            <h1 class="text-4xl font-bold text-gray-900">{{ $course->fullname }}</h1>
            <p class="text-gray-600 mt-1">Gestion et administration du cours</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-lg shadow-lg border-b">
            <nav class="flex border-b overflow-x-auto">
                <button onclick="switchTab('overview')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-indigo-600 text-indigo-600 font-semibold" data-tab="overview">
                    <i class="fas fa-info-circle"></i> Aperçu
                </button>
                <button onclick="switchTab('sections')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="sections">
                    <i class="fas fa-book"></i> Sections
                </button>
                <button onclick="switchTab('participants')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="participants">
                    <i class="fas fa-users"></i> Participants
                </button>
                <button onclick="switchTab('announcements')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="announcements">
                    <i class="fas fa-bullhorn"></i> Annonces
                </button>
                <button onclick="switchTab('documents')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="documents">
                    <i class="fas fa-file"></i> Documents
                </button>
                <button onclick="switchTab('competencies')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="competencies">
                    <i class="fas fa-star"></i> Compétences
                </button>
                <button onclick="switchTab('settings')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="settings">
                    <i class="fas fa-cog"></i> Paramètres
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="mt-6">
            <!-- Overview Tab -->
            <div id="overview" class="tab-content space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <p class="text-gray-600 text-sm">Participants</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $participants->where('status', 1)->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <p class="text-gray-600 text-sm">Annonces</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $announcements->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <p class="text-gray-600 text-sm">Documents</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $documents->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                        <p class="text-gray-600 text-sm">Compétences</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $competencies->count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Informations du cours</h2>
                        <button onclick="switchTab('settings')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                            <i class="fas fa-edit"></i> Modifier les paramètres
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Général -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Général</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Nom court</dt>
                                    <dd class="text-gray-900 mt-1">{{ $course->shortname }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Catégorie</dt>
                                    <dd class="text-gray-900 mt-1">{{ $course->category->name ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">N° d'identification</dt>
                                    <dd class="text-gray-900 mt-1">{{ $course->idnumber ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Visibilité</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $course->visible ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                            <i class="fas {{ $course->visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                            {{ $course->visible ? 'Visible' : 'Masqué' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Dates & Format -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Dates & Format</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Date de début</dt>
                                    <dd class="text-gray-900 mt-1">{{ $course->startdate ? $course->startdate->format('d/m/Y') : '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Date de fin</dt>
                                    <dd class="text-gray-900 mt-1">{{ $course->enddate ? $course->enddate->format('d/m/Y') : '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Format</dt>
                                    <dd class="text-gray-900 mt-1">
                                        @switch($course->format)
                                            @case('topics') Thématique @break
                                            @case('weeks') Hebdomadaire @break
                                            @case('social') Informel @break
                                            @case('singleactivity') Activité unique @break
                                            @default {{ $course->format ?? 'Thématique' }}
                                        @endswitch
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Sections</dt>
                                    <dd class="text-gray-900 mt-1">{{ $sections->count() }} ({{ $course->numsections }} prévues)</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Paramètres avancés -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Paramètres avancés</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Mode de groupe</dt>
                                    <dd class="text-gray-900 mt-1">
                                        @switch($course->groupmode)
                                            @case(1) Groupes visibles @break
                                            @case(2) Groupes séparés @break
                                            @default Pas de groupe
                                        @endswitch
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Taille maximale</dt>
                                    <dd class="text-gray-900 mt-1">
                                        @if($course->maxbytes == 0) Limite du site
                                        @elseif($course->maxbytes >= 1048576) {{ round($course->maxbytes / 1048576) }} Mo
                                        @else {{ $course->maxbytes }} octets
                                        @endif
                                    </dd>
                                </div>
                                @if($course->tags)
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">Tags</dt>
                                    <dd class="mt-1 flex flex-wrap gap-1">
                                        @foreach(explode(',', $course->tags) as $tag)
                                            <span class="bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs px-2 py-0.5 rounded">{{ trim($tag) }}</span>
                                        @endforeach
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Description & Image -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Description</h3>
                            <div class="flex flex-col md:flex-row gap-6">
                                @if($course->image)
                                <div class="flex-shrink-0">
                                    <img src="{{ Storage::url($course->image) }}" alt="Image du cours" class="w-64 h-40 object-cover rounded-lg shadow-sm border border-gray-200">
                                </div>
                                @endif
                                <div class="flex-1 text-gray-700 whitespace-pre-line">
                                    {{ $course->summary ?: 'Pas de description' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Tab -->
            <div id="sections" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-stream text-purple-500"></i> Gestion des sections
                    </h2>
                    <a href="{{ route('sections.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Créer une section
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($sections as $section)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $section->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">
                                    {{ $section->modules->count() }} module(s)
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('sections.edit', [$course, $section]) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sections.destroy', [$course, $section]) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette section ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">Aucune section créée pour le moment.</p>
                        <a href="{{ route('sections.create', $course) }}" class="text-indigo-600 hover:text-indigo-700 font-medium mt-4 inline-block">
                            Créer la première section →
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Participants Tab -->
            <div id="participants" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-users text-teal-500"></i> Gestion des participants
                    </h2>
                    <a href="{{ route('participants.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Ajouter participant
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    @if($participants->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rôle</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Statut</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date d'enrôlement</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium text-gray-900">{{ $participant->user?->name ?? 'Utilisateur supprimé' }}</div>
                                        <div class="text-xs text-gray-500">{{ $participant->user?->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->role === 'ROLE_TEACHER' ? 'bg-purple-100 text-purple-800' : ($participant->role === 'ROLE_STUDENT' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $participant->role === 'ROLE_TEACHER' ? 'Enseignant' : ($participant->role === 'ROLE_STUDENT' ? 'Étudiant' : 'Invité') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $participant->status ? 'Actif' : 'Suspendu' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $participant->enrolled_at ? $participant->enrolled_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('participants.edit', [$course, $participant]) }}" class="text-blue-600 hover:text-blue-800" title="Modifier le rôle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('participants.destroy', [$course, $participant]) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer le désenrôlement?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Désenrôler">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-12 text-center">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4 block"></i>
                        <p class="text-gray-500">Aucun participant pour le moment</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Announcements Tab -->
            <div id="announcements" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-bullhorn text-blue-500"></i> Gestion des annonces
                    </h2>
                    <a href="{{ route('announcements.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Créer annonce
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($announcements as $announcement)
                    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $announcement->subject }}</h3>
                                <p class="text-sm text-gray-600">Posté par {{ $announcement->user?->name ?? 'Auteur inconnu' }} le {{ $announcement->published_at?->format('d/m/Y H:i') ?? 'Non publié' }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $announcement->status ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $announcement->status ? 'Publié' : 'Brouillon' }}
                            </span>
                        </div>
                        <p class="text-gray-700 mb-4">{{ Str::limit($announcement->message, 150) }}</p>
                        <div class="flex gap-2">
                            <a href="{{ route('announcements.edit', [$course, $announcement]) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('announcements.destroy', [$course, $announcement]) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                        <i class="fas fa-bullhorn text-4xl text-gray-300 mb-4 block"></i>
                        <p class="text-gray-500">Aucune annonce</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Documents Tab -->
            <div id="documents" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-file-alt text-green-500"></i> Gestion des documents
                    </h2>
                    <a href="{{ route('documents.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-upload"></i> Ajouter document
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($documents as $document)
                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 border-purple-500 flex justify-between">
                        <div class="flex-1 min-w-0 pr-4">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-file text-2xl text-purple-600 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 truncate" title="{{ $document->filename }}">{{ $document->filename }}</h3>
                                    <p class="text-xs text-gray-500 truncate">{{ $document->mimetype }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ number_format($document->filesize / 1024, 2) }} KB</p>
                        </div>
                        
                        <div class="flex flex-col gap-3 border-l pl-3 justify-center items-center">
                            <a href="{{ route('documents.preview', $document) }}" target="_blank" class="text-gray-500 hover:text-gray-800 transition" title="Visualiser">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('documents.download', [$course, $document]) }}" class="text-blue-500 hover:text-blue-700 transition" title="Télécharger">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('documents.edit', [$course, $document]) }}" class="text-indigo-500 hover:text-indigo-700 transition" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('documents.destroy', [$course, $document]) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-3 bg-white rounded-lg shadow-lg p-12 text-center">
                        <i class="fas fa-file text-4xl text-gray-300 mb-4 block"></i>
                        <p class="text-gray-500">Aucun document</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Competencies Tab -->
            <div id="competencies" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-star text-orange-500"></i> Gestion des compétences
                    </h2>
                    <div class="flex gap-2">
                        <a href="{{ route('competencies.course', $course) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                            <i class="fas fa-cog"></i> Gérer les associations
                        </a>
                        <a href="{{ route('competencies.create', ['course_id' => $course->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <i class="fas fa-plus"></i> Créer compétence
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($competencies as $competency)
                    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $competency->shortname }}</h3>
                                <p class="text-xs text-gray-500">ID: {{ $competency->idnumber }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $competency->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $competency->status ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mb-3">{{ $competency->description }}</p>
                        <a href="{{ route('competencies.edit', $competency) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                    @empty
                    <div class="col-span-2 bg-white rounded-lg shadow p-8 text-center border border-gray-200">
                        <p class="text-gray-500 italic">Aucune compétence associée.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Paramètres du cours</h2>
                    
                    <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf @method('PATCH')

                        <!-- Section: Général -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Général</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="fullname" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                                    <input type="text" id="fullname" name="fullname" required value="{{ $course->fullname }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('fullname') border-red-500 @enderror">
                                </div>

                                <div>
                                    <label for="shortname" class="block text-sm font-semibold text-gray-700 mb-2">Nom court</label>
                                    <input type="text" id="shortname" name="shortname" required value="{{ $course->shortname }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shortname') border-red-500 @enderror">
                                </div>
                                
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Catégorie</label>
                                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <!-- Utiliser les catégories passées depuis le contrôleur, si disponibles, sinon on laisse le choix fixe pour l'instant -->
                                        @isset($categories)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $course->category_id }}" selected>{{ $course->category->name ?? 'Catégorie actuelle' }}</option>
                                        @endisset
                                    </select>
                                </div>

                                <div>
                                    <label for="idnumber" class="block text-sm font-semibold text-gray-700 mb-2">N° d'identification</label>
                                    <input type="text" id="idnumber" name="idnumber" value="{{ $course->idnumber }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="startdate" class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                                    <input type="date" id="startdate" name="startdate" value="{{ $course->startdate?->format('Y-m-d') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('startdate') border-red-500 @enderror">
                                </div>

                                <div>
                                    <label for="enddate" class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                                    <input type="date" id="enddate" name="enddate" value="{{ $course->enddate?->format('Y-m-d') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('enddate') border-red-500 @enderror">
                                </div>
                                
                                <div>
                                    <label for="visible" class="block text-sm font-semibold text-gray-700 mb-2">Visibilité</label>
                                    <select name="visible" id="visible" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="1" {{ $course->visible == 1 ? 'selected' : '' }}>Visible</option>
                                        <option value="0" {{ $course->visible == 0 ? 'selected' : '' }}>Masqué</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Description & Image -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Description & Image</h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                    <textarea id="summary" name="summary" rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $course->summary }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Image du cours</label>
                                    <div class="flex items-center gap-4">
                                        @if($course->image)
                                        <img src="{{ Storage::url($course->image) }}" alt="Aperçu" class="w-20 h-20 object-cover rounded shadow border border-gray-200">
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver l'image actuelle.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section: Format & Apparence -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Format & Apparence</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="format" class="block text-sm font-semibold text-gray-700 mb-2">Format du cours</label>
                                    <select name="format" id="format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="topics" {{ $course->format == 'topics' ? 'selected' : '' }}>Thématique</option>
                                        <option value="weeks" {{ $course->format == 'weeks' ? 'selected' : '' }}>Hebdomadaire</option>
                                        <option value="social" {{ $course->format == 'social' ? 'selected' : '' }}>Informel</option>
                                        <option value="singleactivity" {{ $course->format == 'singleactivity' ? 'selected' : '' }}>Activité unique</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="numsections" class="block text-sm font-semibold text-gray-700 mb-2">Nombre de sections</label>
                                    <input type="number" id="numsections" name="numsections" required value="{{ $course->numsections }}" min="0" max="52"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="hiddensections" class="block text-sm font-semibold text-gray-700 mb-2">Sections cachées</label>
                                    <select name="hiddensections" id="hiddensections" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0" {{ $course->hiddensections == 0 ? 'selected' : '' }}>Invisibles</option>
                                        <option value="1" {{ $course->hiddensections == 1 ? 'selected' : '' }}>Visibles sous forme condensée</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="coursedisplay" class="block text-sm font-semibold text-gray-700 mb-2">Mise en page du cours</label>
                                    <select name="coursedisplay" id="coursedisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0" {{ $course->coursedisplay == 0 ? 'selected' : '' }}>Toutes les sections sur une page</option>
                                        <option value="1" {{ $course->coursedisplay == 1 ? 'selected' : '' }}>Une section par page</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="lang" class="block text-sm font-semibold text-gray-700 mb-2">Langue imposée</label>
                                    <select name="lang" id="lang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Ne pas imposer</option>
                                        <option value="fr" {{ $course->lang == 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ $course->lang == 'en' ? 'selected' : '' }}>Anglais</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="newsitems" class="block text-sm font-semibold text-gray-700 mb-2">Nombre d'annonces</label>
                                    <input type="number" id="newsitems" name="newsitems" value="{{ $course->newsitems }}" min="0" max="10"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Section: Groupes, Achèvement & Fichiers -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Groupes, Achèvement & Fichiers</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="groupmode" class="block text-sm font-semibold text-gray-700 mb-2">Mode de groupe</label>
                                    <select name="groupmode" id="groupmode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0" {{ $course->groupmode == 0 ? 'selected' : '' }}>Pas de groupe</option>
                                        <option value="1" {{ $course->groupmode == 1 ? 'selected' : '' }}>Groupes visibles</option>
                                        <option value="2" {{ $course->groupmode == 2 ? 'selected' : '' }}>Groupes séparés</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="groupmodeforce" class="block text-sm font-semibold text-gray-700 mb-2">Imposer le mode de groupe</label>
                                    <select name="groupmodeforce" id="groupmodeforce" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="1" {{ $course->groupmodeforce == 1 ? 'selected' : '' }}>Oui</option>
                                        <option value="0" {{ $course->groupmodeforce == 0 ? 'selected' : '' }}>Non</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="defaultgroupingid" class="block text-sm font-semibold text-gray-700 mb-2">Groupement par défaut</label>
                                    <select name="defaultgroupingid" id="defaultgroupingid" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0" {{ $course->defaultgroupingid == 0 ? 'selected' : '' }}>Aucun</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="maxbytes" class="block text-sm font-semibold text-gray-700 mb-2">Taille maximale des fichiers</label>
                                    <select name="maxbytes" id="maxbytes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0" {{ $course->maxbytes == 0 ? 'selected' : '' }}>Limite du site</option>
                                        <option value="104857600" {{ $course->maxbytes == 104857600 ? 'selected' : '' }}>100 Mo</option>
                                        <option value="52428800" {{ $course->maxbytes == 52428800 ? 'selected' : '' }}>50 Mo</option>
                                        <option value="10485760" {{ $course->maxbytes == 10485760 ? 'selected' : '' }}>10 Mo</option>
                                        <option value="2097152" {{ $course->maxbytes == 2097152 ? 'selected' : '' }}>2 Mo</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="enablecompletion" class="block text-sm font-semibold text-gray-700 mb-2">Suivi d'achèvement</label>
                                    <select name="enablecompletion" id="enablecompletion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="1" {{ $course->enablecompletion == 1 ? 'selected' : '' }}>Activé</option>
                                        <option value="0" {{ $course->enablecompletion == 0 ? 'selected' : '' }}>Désactivé</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="showcompletionconditions" class="block text-sm font-semibold text-gray-700 mb-2">Conditions d'achèvement</label>
                                    <select name="showcompletionconditions" id="showcompletionconditions" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="1" {{ $course->showcompletionconditions == 1 ? 'selected' : '' }}>Affichées</option>
                                        <option value="0" {{ $course->showcompletionconditions == 0 ? 'selected' : '' }}>Masquées</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">Tags</label>
                                    <input type="text" id="tags" name="tags" value="{{ $course->tags }}" placeholder="Séparés par des virgules"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md">
                                <i class="fas fa-check mr-2"></i> Sauvegarder les modifications
                            </button>
                            <a href="{{ route('courses.index') }}" class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
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

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const tabName = window.location.hash.substring(1);
        if (document.querySelector(`.tab-btn[data-tab="${tabName}"]`)) {
            switchTab(tabName);
        }
    }
});
</script>
@endsection
