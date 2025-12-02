@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editer une Soumission</h1>
    <form action="{{ route('submissions.update', $submission) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="text" name="status" id="status" class="form-control" value="{{ $submission->status }}" required>
        </div>
        <div class="mb-3">
            <label for="file_path" class="form-label">Chemin d'acces au fichier</label>
            <input type="text" name="file_path" id="file_path" class="form-control" value="{{ $submission->file_path }}" required>
        </div>
        <div class="mb-3">
            <label for="assignment_id" class="form-label">Evaluation</label>
            <select name="assignment_id" id="assignment_id" class="form-select" required>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" {{ $submission->assignment_id == $assignment->id ? 'selected' : '' }}>
                        {{ $assignment->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="student_id" class="form-label">Elève</label>
            <select name="student_id" id="student_id" class="form-select" required>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ $submission->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->username }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
