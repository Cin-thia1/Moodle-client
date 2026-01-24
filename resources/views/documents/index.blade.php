@extends('layouts.app')

@section('title', 'Documents - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📄 Documents</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
            @can('upload_document')
            <a href="{{ route('documents.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-upload"></i> Télécharger
            </a>
            @endcan
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($documents as $doc)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-file text-blue-500 text-xl"></i>
                            <h3 class="font-semibold text-gray-900 truncate">{{ $doc->filename }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">{{ \App\Helpers\FileHelper::formatBytes($doc->filesize) }}</p>
                        <p class="text-xs text-gray-400">Créé par {{ $doc->creator->name ?? 'Inconnu' }}</p>
                    </div>
                    @can('edit_document')
                    <div class="ml-2 flex gap-1">
                        <a href="{{ route('documents.download', [$course, $doc]) }}" class="text-blue-600 hover:text-blue-800" title="Télécharger">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('documents.edit', [$course, $doc]) }}" class="text-green-600 hover:text-green-800" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('documents.destroy', [$course, $doc]) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer" onclick="return confirm('Confirmer?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('documents.download', [$course, $doc]) }}" class="text-blue-600 hover:text-blue-800 ml-2">
                        <i class="fas fa-download"></i>
                    </a>
                    @endcan
                </div>
            </div>
            @empty
            <div class="col-span-full bg-gray-100 rounded-lg p-8 text-center">
                <i class="fas fa-file text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-600">Aucun document</p>
            </div>
            @endforelse
        </div>

        @can('upload_document')
        <div class="mt-8 text-center">
            <button onclick="document.getElementById('syncForm').submit()" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mx-auto">
                <i class="fas fa-sync"></i> Synchroniser depuis Moodle
            </button>
            <form id="syncForm" action="{{ route('documents.sync', $course) }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
