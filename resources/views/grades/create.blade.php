@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter un Grade</h1>
    <form action="{{ route('grades.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="grade" class="form-label">Note</label>
            <input type="number" class="form-control" id="grade" name="grade" required>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Commentaire</label>
            <textarea class="form-control" id="comment" name="comment"></textarea>
        </div>
        <div class="mb-3">
            <label for="submission_id" class="form-label">Submission</label>
            <select class="form-select" id="submission_id" name="submission_id" required>
                @foreach($submissions as $submission)
                    <option value="{{ $submission->id }}">{{ $submission->id }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Professeur</label>
            <select class="form-select" id="teacher_id" name="teacher_id" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->username }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="{{ route('grades.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
