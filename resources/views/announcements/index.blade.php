@extends('layouts.app')

@section('title', 'Annonces - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📢 Annonces</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
            @can('create_announcement')
            <a href="{{ route('announcements.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Nouvelle annonce
            </a>
            @endcan
        </div>

        <div class="grid gap-6">
            @forelse($announcements as $announcement)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition border-l-4 border-indigo-600 p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $announcement->subject }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Par <span class="font-medium">{{ $announcement->author?->name ?? 'Auteur inconnu' }}</span>
                            le {{ optional($announcement->published_at ?? $announcement->created_at)->format('d/m/Y à H:i') ?? 'date inconnue' }}
                        </p>
                        <div class="mt-4 text-gray-700 line-clamp-3">
                            {{ $announcement->message }}
                        </div>
                    </div>
                    @can('edit_announcement')
                    <div class="ml-4 flex gap-2">
                        <a href="{{ route('announcements.edit', [$course, $announcement]) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('announcements.destroy', [$course, $announcement]) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Confirmer?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
            @empty
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-600">Aucune annonce pour le moment</p>
            </div>
            @endforelse
        </div>

        @can('create_announcement')
        <div class="mt-8 text-center">
            <button onclick="document.getElementById('syncForm').submit()" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mx-auto">
                <i class="fas fa-sync"></i> Synchroniser depuis Moodle
            </button>
            <form id="syncForm" action="{{ route('announcements.sync', $course) }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
