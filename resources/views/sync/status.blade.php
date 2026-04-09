@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header avec titre et actions -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Synchronisation</h1>
            <p class="text-gray-600 mt-2">Statut et gestion de la synchronisation avec Moodle</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('sync.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Synchroniser maintenant
                </button>
            </form>
            <a href="{{ route('sync.queue') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                📋 Queue
            </a>
            <a href="{{ route('sync.conflicts') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                ⚠️ Conflits
            </a>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistiques principales en grille -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Total en queue -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">Total en queue</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-2">opérations</p>
        </div>

        <!-- En attente -->
        <div class="bg-yellow-50 rounded-lg shadow p-6 border-l-4 border-yellow-400">
            <h3 class="text-sm font-semibold text-yellow-600 mb-2">En attente</h3>
            <p class="text-3xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            <p class="text-xs text-yellow-500 mt-2">à synchroniser</p>
        </div>

        <!-- Synchronisées -->
        <div class="bg-green-50 rounded-lg shadow p-6 border-l-4 border-green-400">
            <h3 class="text-sm font-semibold text-green-600 mb-2">Synchronisées</h3>
            <p class="text-3xl font-bold text-green-700">{{ $stats['synced'] }}</p>
            <p class="text-xs text-green-500 mt-2">réussites</p>
        </div>

        <!-- Erreurs -->
        <div class="bg-red-50 rounded-lg shadow p-6 border-l-4 border-red-400">
            <h3 class="text-sm font-semibold text-red-600 mb-2">Erreurs</h3>
            <p class="text-3xl font-bold text-red-700">{{ $stats['error'] }}</p>
            <p class="text-xs text-red-500 mt-2">à corriger</p>
        </div>
    </div>

    <!-- Entités locales en attente -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Entités en attente de synchronisation</h2>
        
        @if($stats['total_pending_entities'] > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($stats['pending_entities'] as $entity => $count)
                    @if($count > 0)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-orange-600">{{ $count }}</p>
                            <p class="text-sm text-gray-600 mt-1 capitalize">{{ ucfirst(str_replace('_', ' ', $entity)) }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
            <p class="text-sm text-gray-600 mt-4">
                <strong>Total:</strong> {{ $stats['total_pending_entities'] }} entité(s) locale(s) en attente
            </p>
        @else
            <p class="text-green-600">✓ Toutes les entités sont synchronisées !</p>
        @endif
    </div>

    <!-- Opérations en attente -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Opérations en attente</h2>
            <a href="{{ route('sync.queue') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                Voir tout →
            </a>
        </div>

        @if($pending->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Entité</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Opération</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Crée</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ $item->entity_type }}: {{ $item->entity_id }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold 
                                        @if($item->operation === 'create') bg-blue-100 text-blue-700
                                        @elseif($item->operation === 'update') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst($item->operation) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                                        <span class="text-xs text-gray-600">En attente</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-8">
                ✓ Aucune opération en attente
            </p>
        @endif
    </div>

    <!-- Syncs récentes réussies -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Syncs récentes</h2>

        @if($recent->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Entité</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Opération</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Synchronisée</th>
                            <th class="text-left py-2 px-3 font-semibold text-gray-600">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ $item->entity_type }}: {{ $item->entity_id }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold 
                                        @if($item->operation === 'create') bg-blue-100 text-blue-700
                                        @elseif($item->operation === 'update') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst($item->operation) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->processed_at)->diffForHumans() }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                                        <span class="text-xs text-green-600">Synchronisée</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-8">
                Aucune synchronisation réussie encore
            </p>
        @endif
    </div>
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
