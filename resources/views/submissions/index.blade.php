@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Soumissions</h1>
    <a href="{{ route('submissions.create') }}" class="btn btn-primary mb-3">Créer une nouvelle Soumission</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Chemin d'acces au fichier</th>
                <th>Evaluation</th>
                <th>Elève</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
                <tr>
                    <td>{{ $submission->id }}</td>
                    <td>{{ $submission->status }}</td>
                    <td>{{ $submission->file_path }}</td>
                    <td>{{ $submission->assignment->name }}</td>
                    <td>{{ $submission->student->username }}</td>
                    <td>
                        <a href="{{ route('submissions.show', $submission) }}" class="btn btn-info">Voir</a>
                        <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-warning">Editer</a>
                        <form action="{{ route('submissions.destroy', $submission) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
