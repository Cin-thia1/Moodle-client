@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Section 1 : Informations de profil (pleine largeur) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Section 2 : Token Moodle --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-moodle-token-form')
                </div>
            </div>

            {{-- Section 3 : Mot de passe --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Section 4 : Suppression de compte --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
