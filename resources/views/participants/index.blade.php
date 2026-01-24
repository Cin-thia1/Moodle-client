@extends('layouts.app')

@section('title', 'Participants - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">👥 Participants</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
            @can('enrol_user')
            <a href="{{ route('participants.create', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Enrôler
            </a>
            @endcan
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rôle</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Statut</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date d'enrôlement</th>
                        @can('manage_participants')
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $participant)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $participant->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->role === 'ROLE_TEACHER' ? 'bg-purple-100 text-purple-800' : ($participant->role === 'ROLE_STUDENT' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $participant->role === 'ROLE_TEACHER' ? 'Enseignant' : ($participant->role === 'ROLE_STUDENT' ? 'Étudiant' : 'Invité') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $participant->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $participant->status ? 'Actif' : 'Suspendu' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $participant->enrolled_at ? $participant->enrolled_at->format('d/m/Y') : '-' }}
                        </td>
                        @can('manage_participants')
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('participants.edit', [$course, $participant]) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('participants.destroy', [$course, $participant]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Confirmer le désenrôlement?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                            Aucun participant
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @can('manage_participants')
        <div class="mt-8 text-center">
            <button onclick="document.getElementById('syncForm').submit()" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mx-auto">
                <i class="fas fa-sync"></i> Synchroniser depuis Moodle
            </button>
            <form id="syncForm" action="{{ route('participants.sync', $course) }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
