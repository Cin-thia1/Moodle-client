@extends('layouts.app')

@section('title', 'Éditer catégorie')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- En-tête -->
        <header class="mb-10">
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-3">
                <i class="fas fa-arrow-left"></i> Retour aux catégories
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight flex items-center gap-4">
                <i class="fas fa-folder-open text-indigo-500"></i> Éditer « {{ $category->name }} »
            </h1>
        </header>

        <!-- Statut de synchronisation -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6 max-w-3xl flex items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-sync-alt text-gray-400"></i>
                <span class="text-sm text-gray-600">Statut :</span>
                @if($category->sync_status === 'pending')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock"></i> {{ ucfirst($category->sync_action) }} en attente
                    </span>
                @elseif($category->sync_status === 'synced')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <i class="fas fa-check-circle"></i> Synchronisée
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle"></i> Conflit
                    </span>
                @endif
            </div>
            @if($category->synced_at)
                <span class="text-xs text-gray-400 ml-auto">
                    Dernière synchro : {{ $category->synced_at->format('d/m/Y à H:i') }}
                </span>
            @endif
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden max-w-3xl">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-5">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-edit"></i> Modifier les informations
                </h2>
            </div>

            <form action="{{ route('categories.update', $category->id) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="descriptionformat" value="{{ old('descriptionformat', $category->descriptionformat ?? 1) }}">

                <!-- Catégorie parente -->
                <div>
                    <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sitemap text-gray-400 mr-1"></i> Catégorie parente
                    </label>
                    <select id="parent_id" name="parent_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-gray-900">
                        <option value="">Aucune (Niveau supérieur)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-gray-400 mr-1"></i> Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- ID Number -->
                <div>
                    <label for="idnumber" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-hashtag text-gray-400 mr-1"></i> Nombre ID
                        <span class="text-xs text-gray-400 font-normal ml-1">(optionnel)</span>
                    </label>
                    <input type="text" id="idnumber" name="idnumber" value="{{ old('idnumber', $category->idnumber) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="Identifiant unique">
                    @error('idnumber')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left text-gray-400 mr-1"></i> Description
                        <span class="text-xs text-gray-400 font-normal ml-1">(optionnel)</span>
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-y"
                              placeholder="Décrivez cette catégorie...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold py-3 px-8 rounded-xl hover:bg-gray-200 transition-all duration-300">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
