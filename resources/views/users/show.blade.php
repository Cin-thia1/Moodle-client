@extends('layouts.app')

@section('content')
<div class="container bg-white p-6 rounded">
    <!-- Bouton Retour -->
    <div class="p-6">
                    <a href="{{ route('users.index') }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
    <h1>Details sur l'utilisateur</h1>
    <p><strong>Nom:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <!-- <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a> -->
</div>
@endsection
