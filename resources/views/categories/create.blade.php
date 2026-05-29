@extends('layouts.app')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- En-tête -->
        <header class="mb-10">
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-3">
                <i class="fas fa-arrow-left"></i> Retour aux catégories
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight flex items-center gap-4">
                <i class="fas fa-folder-plus text-indigo-500"></i> Nouvelle catégorie
            </h1>
            <p class="text-gray-500 mt-2">Créez une nouvelle catégorie pour organiser vos cours.</p>
        </header>

        <!-- Formulaire -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden max-w-3xl">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-5">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-info-circle"></i> Informations de la catégorie
                </h2>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="descriptionformat" value="1">

                <!-- Catégorie parente -->
                <div>
                    <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sitemap text-gray-400 mr-1"></i> Catégorie parente
                    </label>
                    <select id="parent_id" name="parent_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-gray-900">
                        <option value="">Aucune (Niveau supérieur)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
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
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="Ex: Sciences, Mathématiques, Langues...">
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
                    <input type="text" id="idnumber" name="idnumber" value="{{ old('idnumber') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="Identifiant unique (ex: CAT-001)">
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
                              placeholder="Décrivez cette catégorie...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-check"></i> Créer la catégorie
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
