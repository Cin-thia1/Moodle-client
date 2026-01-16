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
    <h1>Sections</h1>
    <a href="{{ route('sections.create') }}" class="btn btn-primary mb-3">Ajouter une Section</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Cours</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sections as $section)
                <tr>
                    <td>{{ $section->id }}</td>
                    <td>{{ $section->name }}</td>
                    <td>{{ $section->course->fullname }}</td>
                    <td>
                        <a href="{{ route('sections.show', $section) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('sections.edit', $section) }}" class="btn btn-warning btn-sm">Editer</a>
                        <form action="{{ route('sections.destroy', $section) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
