@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Modifier l\'Utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nom -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Nom')" />
                            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Rôles (Sélection multiple) -->
                        <div class="mt-4">
                            <x-input-label for="roles" :value="__('Rôles')" />
                            <select name="roles[]" id="roles" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" multiple>
                                <option value="ROLE_USER" {{ in_array('ROLE_USER', $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>Utilisateur</option>
                                <option value="ROLE_STUDENT" {{ in_array('ROLE_STUDENT', $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>Élève</option>
                                <option value="ROLE_TEACHER" {{ in_array('ROLE_TEACHER', $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>Enseignant</option>
                                <option value="ROLE_ADMIN" {{ in_array('ROLE_ADMIN', $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>Administrateur</option>
                            </select>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Maintenez <kbd class="px-1 text-xs bg-gray-200 rounded">Ctrl</kbd> (ou <kbd class="px-1 text-xs bg-gray-200 rounded">Cmd</kbd> sur Mac) pour sélectionner plusieurs rôles.</p>
                        </div>

                        <!-- Bouton d'action -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
