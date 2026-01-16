@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des Grades</h1>
    <a href="{{ route('grades.create') }}" class="btn btn-primary mb-3">Ajouter un Grade</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Submission</th>
                <th>Professeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
                <tr>
                    <td>{{ $grade->id }}</td>
                    <td>{{ $grade->grade }}</td>
                    <td>{{ $grade->comment }}</td>
                    <td>{{ $grade->submission->id }}</td>
                    <td>{{ $grade->teacher->username }}</td>
                    <td>
                        <a href="{{ route('grades.show', $grade->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('grades.edit', $grade->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('grades.destroy', $grade->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
