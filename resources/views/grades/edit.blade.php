@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier le Grade</h1>
    <form action="{{ route('grades.update', $grade->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="grade" class="form-label">Note</label>
            <input type="number" class="form-control" id="grade" name="grade" value="{{ $grade->grade }}" required>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Commentaire</label>
            <textarea class="form-control" id="comment" name="comment">{{ $grade->comment }}</textarea>
        </div>
        <div class="mb-3">
            <label for="submission_id" class="form-label">Submission</label>
            <select class="form-select" id="submission_id" name="submission_id" required>
                @foreach($submissions as $submission)
                    <option value="{{ $submission->id }}" {{ $grade->submission_id == $submission->id ? 'selected' : '' }}>
                        {{ $submission->id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Professeur</label>
            <select class="form-select" id="teacher_id" name="teacher_id" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ $grade->teacher_id == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->username }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('grades.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
