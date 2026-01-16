@extends('layouts.app') <!-- Utilisation du layout Blade principal -->

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <!-- Bouton pour ajouter un utilisateur -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-800">Liste des Utilisateurs</h3>
                        <a href="{{ route('admin.users.create') }}" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                            + Ajouter un Utilisateur
                        </a>
                    </div>

                    <!-- Message de succès -->
                    @if (session('success'))
                        <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg shadow-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Tableau des utilisateurs -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nom</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Rôle</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-800 border-b">{{ $user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800 border-b">{{ $user->email }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800 border-b">
                                            @foreach ($user->roles as $role)
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 rounded-lg">
                                                    <!-- Affichage explicite des rôles en français -->
                                                    @switch($role->name)
                                                        @case('ROLE_USER')
                                                            Utilisateur
                                                            @break
                                                        @case('ROLE_STUDENT')
                                                            Élève
                                                            @break
                                                        @case('ROLE_TEACHER')
                                                            Enseignant
                                                            @break
                                                        @case('ROLE_ADMIN')
                                                            Administrateur
                                                            @break
                                                        @default
                                                            {{ $role->name }}
                                                    @endswitch
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 border-b">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="text-blue-500 hover:text-blue-700 font-medium">
                                                Modifier
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                                  method="POST" 
                                                  class="inline-block ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-500 hover:text-red-700 font-medium"
                                                        onclick="return confirm('Êtes-vous sûr ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-600">
                                            Aucun utilisateur trouvé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $users->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
