@extends('layouts.app')

@section('title', 'Sections - ' . $course->fullname)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour au cours
                </a>
                <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-stream text-purple-500"></i> Sections
                </h1>
                <p class="text-gray-600 mt-1">Gérez les sections du cours {{ $course->fullname }}</p>
            </div>
            <a href="{{ route('sections.create', $course) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Nouvelle section
            </a>
        </div>

        <!-- Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Sections List -->
        @if ($sections->count())
            <div class="space-y-4">
                @foreach ($sections as $section)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $section->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">
                                    {{ $section->modules->count() }} module(s)
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('sections.edit', [$course, $section]) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sections.destroy', [$course, $section]) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette section ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">Aucune section créée pour le moment.</p>
                <a href="{{ route('sections.create', $course) }}" class="text-indigo-600 hover:text-indigo-700 font-medium mt-4 inline-block">
                    Créer la première section →
                </a>
            </div>
        @endif
    </div>
</div>
