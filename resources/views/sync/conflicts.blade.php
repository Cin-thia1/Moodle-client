@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Conflits de Synchronisation</h1>
            <p class="text-gray-600 mt-2">Opérations échouées nécessitant une intervention</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('sync.retry') }}" method="POST" class="inline" onsubmit="return confirm('Relancer tous les conflits ?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 flex items-center gap-2">
                    🔄 Relancer tous
                </button>
            </form>
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

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Infos sur les conflits -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-red-800">Vous avez {{ $conflicts->total() }} conflit(s)</h3>
                <p class="text-red-700 mt-2">
                    Ces opérations de synchronisation n'ont pas pu être complétées. Vous pouvez les relancer manuellement ou les résoudre en utilisant les stratégies appropriées (priorité serveur ou client).
                </p>
            </div>
        </div>
    </div>

    <!-- Tableau des conflits -->
    @if($conflicts->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <table class="w-full">
                <thead class="bg-red-100 border-b border-red-300">
                    <tr>
                        <th class="text-left py-3 px-6 font-semibold text-red-900">Entité</th>
                        <th class="text-left py-3 px-6 font-semibold text-red-900">Opération</th>
                        <th class="text-left py-3 px-6 font-semibold text-red-900">Message d'erreur</th>
                        <th class="text-left py-3 px-6 font-semibold text-red-900">Créée</th>
                        <th class="text-center py-3 px-6 font-semibold text-red-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conflicts as $conflict)
                        <tr class="border-b border-gray-100 hover:bg-red-50 transition">
                            <!-- Entité -->
                            <td class="py-4 px-6">
                                <span class="font-mono text-xs bg-red-100 px-2 py-1 rounded block w-fit">
                                    {{ $conflict->entity_type }}<br>ID: {{ $conflict->entity_id }}
                                </span>
                            </td>

                            <!-- Opération -->
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded text-xs font-semibold inline-block
                                    @if($conflict->operation === 'create') bg-blue-100 text-blue-700
                                    @elseif($conflict->operation === 'update') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($conflict->operation) }}
                                </span>
                            </td>

                            <!-- Message d'erreur -->
                            <td class="py-4 px-6">
                                @if($conflict->error_message)
                                    <div class="bg-red-50 border border-red-200 rounded p-2 text-xs text-red-700 max-h-16 overflow-y-auto">
                                        {{ $conflict->error_message }}
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">Aucun message disponible</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="py-4 px-6 text-sm text-gray-600 whitespace-nowrap">
                                {{ $conflict->created_at->format('d/m/Y H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex gap-2 justify-center flex-wrap">
                                    <!-- Bouton Relancer -->
                                    <form action="{{ route('sync.retry') }}" method="POST" class="inline" onsubmit="return confirm('Relancer cette opération ?');">
                                        @csrf
                                        <input type="hidden" name="entity_id" value="{{ $conflict->entity_id }}">
                                        <button type="submit" class="px-3 py-1 text-xs bg-orange-500 text-white rounded hover:bg-orange-600 font-semibold">
                                            🔄 Relancer
                                        </button>
                                    </form>

                                    <!-- Bouton Résoudre (Serveur) -->
                                    <form action="{{ route('sync.resolve', ['entityType' => $conflict->entity_type, 'entityId' => $conflict->entity_id]) }}" method="POST" class="inline" onsubmit="return confirm('Utiliser la version serveur ?');">
                                        @csrf
                                        <input type="hidden" name="strategy" value="server_wins">
                                        <button type="submit" class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 font-semibold">
                                            📥 Serveur
                                        </button>
                                    </form>

                                    <!-- Bouton Résoudre (Client) -->
                                    <form action="{{ route('sync.resolve', ['entityType' => $conflict->entity_type, 'entityId' => $conflict->entity_id]) }}" method="POST" class="inline" onsubmit="return confirm('Utiliser la version locale ?');">
                                        @csrf
                                        <input type="hidden" name="strategy" value="client_wins">
                                        <button type="submit" class="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600 font-semibold">
                                            📤 Client
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-lg shadow p-4">
            {{ $conflicts->links() }}
        </div>
    @else
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow p-12 text-center border border-green-200">
            <svg class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="text-2xl font-bold text-green-800 mb-2">Excellent !</h3>
            <p class="text-green-700 text-lg">
                Aucun conflit de synchronisation. Tout fonctionne correctement.
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
