@extends('layouts.app')

@section('title', 'Modifier annonce')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('announcements.index', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Modifier l'annonce</h1>

            <form action="{{ route('announcements.update', [$course, $announcement]) }}" method="POST" class="space-y-6">
                @csrf @method('PATCH')

                <div>
                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Titre</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $announcement->subject) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="8" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('message', $announcement->message) }}</textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-check mr-2"></i> Mettre à jour
                    </button>
                    <a href="{{ route('announcements.index', $course) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
