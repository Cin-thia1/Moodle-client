@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- En-tête -->
        <header class="mb-10">
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-3">
                <i class="fas fa-arrow-left"></i> Retour aux catégories
            </a>
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight flex items-center gap-4">
                        <i class="fas fa-folder text-indigo-500"></i> {{ $category->name }}
                    </h1>
                    @if($category->parent)
                        <p class="text-gray-500 mt-2 flex items-center gap-2">
                            <i class="fas fa-level-up-alt fa-rotate-90 text-gray-400"></i>
                            Sous-catégorie de
                            <a href="{{ route('categories.show', $category->parent->id) }}" class="text-indigo-600 hover:underline font-semibold">
                                {{ $category->parent->name }}
                            </a>
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('categories.edit', $category->id) }}" class="inline-flex items-center gap-2 bg-amber-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:bg-amber-600 transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-edit"></i> Éditer
                    </a>
                    <a href="{{ route('categories.courses', $category->id) }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-book"></i> Voir les cours
                    </a>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Informations générales -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-info-circle"></i> Informations
                        </h2>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">ID Local</p>
                                <p class="text-lg font-bold text-gray-900">#{{ $category->id }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">ID Moodle</p>
                                <p class="text-lg font-bold text-gray-900">{{ $category->moodle_id ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nombre ID</p>
                                <p class="text-lg font-bold text-gray-900">{{ $category->idnumber ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catégorie parente</p>
                                @if($category->parent)
                                    <a href="{{ route('categories.show', $category->parent->id) }}" class="text-lg font-bold text-indigo-600 hover:underline">
                                        {{ $category->parent->name }}
                                    </a>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Niveau supérieur</p>
                                @endif
                            </div>
                        </div>

                        @if($category->description)
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Description</p>
                                <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 rounded-xl p-6">
                                    {!! nl2br(e($category->description)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sous-catégories -->
                @if($category->children->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-sitemap"></i> Sous-catégories ({{ $category->children->count() }})
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child->id) }}" class="flex items-center justify-between p-5 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                                        <i class="fas fa-folder text-purple-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $child->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $child->courses()->count() }} cours</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-300"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-8">
                <!-- Statut de synchronisation -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-sync-alt text-gray-400"></i> Synchronisation
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Statut</span>
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
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Dernière synchro</span>
                            <span class="text-sm font-medium text-gray-900">{{ $category->synced_at?->format('d/m/Y H:i') ?? 'Jamais' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-gray-400"></i> Statistiques
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Cours</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ $category->courses()->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Sous-catégories</span>
                            <span class="text-2xl font-bold text-purple-600">{{ $category->children->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-gray-400"></i> Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('categories.edit', $category->id) }}" class="w-full inline-flex items-center justify-center gap-2 bg-amber-50 text-amber-700 font-semibold py-3 px-4 rounded-xl hover:bg-amber-100 transition-all duration-200">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Les sous-catégories seront également supprimées.')" class="w-full inline-flex items-center justify-center gap-2 bg-red-50 text-red-700 font-semibold py-3 px-4 rounded-xl hover:bg-red-100 transition-all duration-200">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
