@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('categories.index') }}" class="text-blue-500 hover:text-blue-700 mb-4">← Retour aux catégories</a>

    <h1 class="text-3xl font-bold mb-6">{{ $category->name }}</h1>

    <div class="bg-white rounded shadow p-6 mb-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">ID local</p>
                <p class="font-bold">{{ $category->id }}</p>
            </div>
            <div>
                <p class="text-gray-600">ID Moodle</p>
                <p class="font-bold">{{ $category->moodle_id ?? 'Non synchronisé' }}</p>
            </div>
            <div>
                <p class="text-gray-600">Statut de sync</p>
                <p class="font-bold">
                    @if($category->sync_status === 'pending')
                        <span class="px-2 py-1 bg-yellow-200 rounded">{{ ucfirst($category->sync_action) }} en attente</span>
                    @elseif($category->sync_status === 'synced')
                        <span class="px-2 py-1 bg-green-200 rounded">Synchronisée</span>
                    @else
                        <span class="px-2 py-1 bg-red-200 rounded">Conflit</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-gray-600">Dernière synchro</p>
                <p class="font-bold">{{ $category->synced_at?->format('d/m/Y H:i') ?? 'Jamais' }}</p>
            </div>
        </div>
    </div>

    <div class="flex space-x-2">
        <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
            Éditer
        </a>
        <a href="{{ route('categories.courses', $category->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Voir les cours
        </a>
    </div>
</div>
@endsection
