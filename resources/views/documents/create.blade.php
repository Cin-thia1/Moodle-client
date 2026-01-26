@extends('layouts.app')

@section('title', 'Télécharger document')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('courses.show', $course) }}#documents" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">

            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Télécharger un document</h1>

            <form action="{{ route('documents.store', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="filename" class="block text-sm font-semibold text-gray-700 mb-2">Nom du document</label>
                    <input type="text" id="filename" name="filename" value="{{ old('filename') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">Fichier</label>
                    <input type="file" id="file" name="file" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-upload mr-2"></i> Télécharger
                    </button>
                    <a href="{{ route('courses.show', $course) }}#documents" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
