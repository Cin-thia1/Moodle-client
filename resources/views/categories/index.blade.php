@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Catégories de cours</h1>
        <a href="{{ route('categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nouvelle catégorie
        </a>
    </div>

    @if($message = session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ $message }}
    </div>
    @endif

    @if($message = session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ $message }}
    </div>
    @endif

    @forelse($categories as $category)
    <div class="bg-white rounded shadow p-4 mb-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">{{ $category->name }}</h3>
                <p class="text-sm text-gray-600">
                    {{ $category->cours()->count() }} cours
                    @if($category->sync_status === 'pending')
                        <span class="text-xs px-2 py-1 bg-yellow-200 rounded">{{ $category->sync_action }} en attente</span>
                    @elseif($category->sync_status === 'synced')
                        <span class="text-xs px-2 py-1 bg-green-200 rounded">Synchronisée</span>
                    @else
                        <span class="text-xs px-2 py-1 bg-red-200 rounded">Conflit</span>
                    @endif
                </p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('categories.show', $category->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                    Voir
                </a>
                <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded text-sm">
                    Éditer
                </a>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Êtes-vous sûr?')" class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded text-sm">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p class="text-gray-600 text-center py-8">Aucune catégorie trouvée</p>
    @endforelse
</div>
@endsection
