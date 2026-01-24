@extends('layouts.app')

@section('title', 'Enrôler utilisateur')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('participants.index', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Enrôler un utilisateur</h1>

            <form action="{{ route('participants.store', $course) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">Utilisateur</label>
                    <select id="user_id" name="user_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Sélectionner --</option>
                        @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Rôle</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="ROLE_TEACHER">Enseignant</option>
                        <option value="ROLE_STUDENT" selected>Étudiant</option>
                        <option value="ROLE_USER">Invité</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-check mr-2"></i> Enrôler
                    </button>
                    <a href="{{ route('participants.index', $course) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
