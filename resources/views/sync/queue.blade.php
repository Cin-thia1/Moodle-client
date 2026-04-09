@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Queue de Synchronisation</h1>
            <p class="text-gray-600 mt-2">Affichage détaillé de toutes les opérations</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sync.status') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Total</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 text-center border border-yellow-200">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-yellow-600 mt-1">En attente</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border border-green-200">
            <p class="text-2xl font-bold text-green-600">{{ $stats['synced'] }}</p>
            <p class="text-xs text-green-600 mt-1">Synchronisées</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4 text-center border border-red-200">
            <p class="text-2xl font-bold text-red-600">{{ $stats['error'] }}</p>
            <p class="text-xs text-red-600 mt-1">Erreurs</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Filtres</h2>
        <form method="GET" action="{{ route('sync.queue') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filtre Entité -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Entité</label>
                <select name="entity_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les entités</option>
                    <option value="categories" @if(request('entity_type') === 'categories') selected @endif>Catégories</option>
                    <option value="courses" @if(request('entity_type') === 'courses') selected @endif>Cours</option>
                    <option value="sections" @if(request('entity_type') === 'sections') selected @endif>Sections</option>
                    <option value="modules" @if(request('entity_type') === 'modules') selected @endif>Modules</option>
                    <option value="participants" @if(request('entity_type') === 'participants') selected @endif>Participants</option>
                    <option value="submissions" @if(request('entity_type') === 'submissions') selected @endif>Soumissions</option>
                    <option value="grades" @if(request('entity_type') === 'grades') selected @endif>Notes</option>
                    <option value="quiz_attempts" @if(request('entity_type') === 'quiz_attempts') selected @endif>Tentatives Quiz</option>
                    <option value="documents" @if(request('entity_type') === 'documents') selected @endif>Documents</option>
                </select>
            </div>

            <!-- Filtre Statut -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending" @if(request('status') === 'pending') selected @endif>En attente</option>
                    <option value="synced" @if(request('status') === 'synced') selected @endif>Synchronisée</option>
                    <option value="error" @if(request('status') === 'error') selected @endif>Erreur</option>
                </select>
            </div>

            <!-- Filtre Opération -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Opération</label>
                <select name="operation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les opérations</option>
                    <option value="create" @if(request('operation') === 'create') selected @endif>Créer</option>
                    <option value="update" @if(request('operation') === 'update') selected @endif>Mettre à jour</option>
                    <option value="delete" @if(request('operation') === 'delete') selected @endif>Supprimer</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Filtrer
                </button>
                <a href="{{ route('sync.queue') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des opérations -->
    @if($entries->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Entité</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Opération</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Statut</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Créée</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <!-- Entité -->
                            <td class="py-4 px-6">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded block w-fit">
                                    {{ $entry->entity_type }}<br>ID: {{ $entry->entity_id }}
                                </span>
                            </td>

                            <!-- Opération -->
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded text-xs font-semibold inline-block
                                    @if($entry->operation === 'create') bg-blue-100 text-blue-700
                                    @elseif($entry->operation === 'update') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($entry->operation) }}
                                </span>
                            </td>

                            <!-- Statut -->
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded text-xs font-semibold inline-flex items-center gap-1
                                    @if($entry->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($entry->status === 'synced') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    <span class="inline-block w-2 h-2 rounded-full
                                        @if($entry->status === 'pending') bg-yellow-400 animate-pulse
                                        @elseif($entry->status === 'synced') bg-green-400
                                        @else bg-red-400
                                        @endif"></span>
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </td>

                            <!-- Date de création -->
                            <td class="py-4 px-6 text-sm text-gray-600">
                                {{ $entry->created_at->format('d/m/Y H:i') }}
                            </td>

                            <!-- Message -->
                            <td class="py-4 px-6 text-sm">
                                @if($entry->error_message)
                                    <span class="text-red-600 font-semibold">{{ $entry->error_message }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-lg shadow p-4">
            {{ $entries->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600 text-lg">
                ✓ Aucune opération trouvée correspondant aux filtres
            </p>
        </div>
    @endif
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection
