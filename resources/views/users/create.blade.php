@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Bouton Retour -->
    <div class="p-6">
                    <a href="{{ url()->previous() }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
    <h1>Créer un utilisateur</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection
