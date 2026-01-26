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
                <button onclick="switchTab('grades')" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-600 hover:text-indigo-600 font-semibold" data-tab="grades">
                    <i class="fas fa-chart-line"></i> Notes
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
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $participants->count() }}</p>
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Informations du cours</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Nom court</dt>
                            <dd class="text-lg text-gray-900 mt-1">{{ $course->shortname }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Sections</dt>
                            <dd class="text-lg text-gray-900 mt-1">{{ $course->numsections }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Date de début</dt>
                            <dd class="text-lg text-gray-900 mt-1">{{ $course->startdate ? $course->startdate->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Date de fin</dt>
                            <dd class="text-lg text-gray-900 mt-1">{{ $course->enddate ? $course->enddate->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-semibold text-gray-600">Description</dt>
                            <dd class="text-gray-700 mt-1">{{ $course->summary ?: 'Pas de description' }}</dd>
                        </div>
                    </dl>
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
                    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file text-2xl text-purple-600"></i>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $document->filename }}</h3>
                                    <p class="text-xs text-gray-500">{{ $document->mimetype }}</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">{{ number_format($document->filesize / 1024, 2) }} KB</p>
                        <div class="flex gap-2">
                            <a href="{{ route('documents.download', [$course, $document]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                            <a href="{{ route('documents.edit', [$course, $document]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('documents.destroy', [$course, $document]) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i> Supprimer
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

            <!-- Grades Tab -->
            <div id="grades" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-graduation-cap text-red-500"></i> Gestion des notes
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('grades.items', $course) }}" class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition border-l-4 border-orange-500">
                        <i class="fas fa-chart-bar text-3xl text-orange-600 mb-3"></i>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Critères d'évaluation</h3>
                        <p class="text-gray-600 text-sm">Gérer les critères d'évaluation</p>
                    </a>
                    <a href="{{ route('grades.gradebook', $course) }}" class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition border-l-4 border-green-500">
                        <i class="fas fa-table text-3xl text-green-600 mb-3"></i>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Carnet de notes</h3>
                        <p class="text-gray-600 text-sm">Voir les notes de tous les étudiants</p>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Critères d'évaluation</h3>
                    <div class="space-y-3">
                        @forelse($gradeItems as $item)
                        <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $item->item_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $item->item_type }}</p>
                            </div>
                            <span class="text-lg font-bold text-indigo-600">/{{ $item->grade_max }}</span>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-6">Aucun critère d'évaluation</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Competencies Tab -->
            <div id="competencies" class="tab-content hidden space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-star text-orange-500"></i> Gestion des compétences
                    </h2>
                    <a href="{{ route('competencies.create', ['course_id' => $course->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Créer compétence
                    </a>
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
                    <div class="col-span-2 bg-white rounded-lg shadow-lg p-12 text-center">
                        <i class="fas fa-star text-4xl text-gray-300 mb-4 block"></i>
                        <p class="text-gray-500">Aucune compétence</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Paramètres du cours</h2>
                    
                    <form action="{{ route('courses.update', $course) }}" method="POST" class="space-y-6">
                        @csrf @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fullname" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                                <input type="text" id="fullname" name="fullname" required value="{{ $course->fullname }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('fullname') border-red-500 @enderror">
                                @error('fullname')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shortname" class="block text-sm font-semibold text-gray-700 mb-2">Nom court</label>
                                <input type="text" id="shortname" name="shortname" required value="{{ $course->shortname }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shortname') border-red-500 @enderror">
                                @error('shortname')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="numsections" class="block text-sm font-semibold text-gray-700 mb-2">Nombre de sections</label>
                                <input type="number" id="numsections" name="numsections" required value="{{ $course->numsections }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('numsections') border-red-500 @enderror">
                                @error('numsections')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="startdate" class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                                <input type="date" id="startdate" name="startdate" value="{{ $course->startdate?->format('Y-m-d') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('startdate') border-red-500 @enderror">
                                @error('startdate')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="enddate" class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                                <input type="date" id="enddate" name="enddate" value="{{ $course->enddate?->format('Y-m-d') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('enddate') border-red-500 @enderror">
                                @error('enddate')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="summary" name="summary" rows="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('summary') border-red-500 @enderror">{{ $course->summary }}</textarea>
                            @error('summary')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                <i class="fas fa-check mr-2"></i> Sauvegarder
                            </button>
                            <a href="{{ route('courses.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
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
</script>
@endsection
