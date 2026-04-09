@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Éditer catégorie</h1>

    <form action="{{ route('categories.update', $category->id) }}" method="POST" class="bg-white rounded shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Nom:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Statut de synchronisation: <strong>{{ ucfirst($category->sync_status) }}</strong>
                @if($category->sync_action)
                    (Action: {{ ucfirst($category->sync_action) }})
                @endif
            </p>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Mettre à jour
            </button>
            <a href="{{ route('categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
