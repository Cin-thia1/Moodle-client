@extends('layouts.app')

@section('title', 'Catégories de cours')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- En-tête -->
        <header class="mb-10">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <a href="{{ route('courses.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center gap-2 mb-3">
                        <i class="fas fa-arrow-left"></i> Retour aux cours
                    </a>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight flex items-center gap-4">
                        <i class="fas fa-th-large text-indigo-500"></i> Catégories de cours
                    </h1>
                    <p class="text-gray-500 mt-2">Organisez vos cours par catégories.</p>
                </div>
                <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-plus-circle"></i> Nouvelle catégorie
                </a>
            </div>
        </header>

        @if($message = session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>{{ $message }}</span>
            </div>
        @endif

        @if($message = session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>{{ $message }}</span>
            </div>
        @endif

        <!-- Liste des catégories -->
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-4 hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="flex items-center justify-between p-6">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-folder text-2xl text-indigo-500"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $category->name }}</h3>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                @if($category->parent)
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-home"></i> Niveau supérieur
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-book text-gray-400"></i> {{ $category->courses()->count() }} cours
                                </span>
                                @if($category->children->count() > 0)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-sitemap text-gray-400"></i> {{ $category->children->count() }} sous-catégories
                                    </span>
                                @endif
                                @if($category->idnumber)
                                    <span class="text-xs text-gray-400">
                                        <i class="fas fa-hashtag"></i> {{ $category->idnumber }}
                                    </span>
                                @endif

                                {{-- Badge de synchronisation --}}
                                @if($category->sync_status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock"></i> {{ $category->sync_action }}
                                    </span>
                                @elseif($category->sync_status === 'synced')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Synchro
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle"></i> Conflit
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('categories.show', $category->id) }}" class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 font-medium py-2 px-4 rounded-xl hover:bg-indigo-100 transition text-sm">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('categories.edit', $category->id) }}" class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 font-medium py-2 px-4 rounded-xl hover:bg-amber-100 transition text-sm">
                            <i class="fas fa-edit"></i> Éditer
                        </a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer cette catégorie ?')" class="inline-flex items-center gap-1 bg-red-50 text-red-700 font-medium py-2 px-4 rounded-xl hover:bg-red-100 transition text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white text-center p-16 rounded-2xl shadow-sm border border-gray-200">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Aucune catégorie</h3>
                <p class="text-gray-500 mb-6">Commencez par créer votre première catégorie pour organiser vos cours.</p>
                <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg hover:bg-indigo-700 transition-all duration-300">
                    <i class="fas fa-plus-circle"></i> Créer une catégorie
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
