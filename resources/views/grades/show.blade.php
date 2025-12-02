@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du Grade</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Grade ID: {{ $grade->id }}</h5>
            <p class="card-text"><strong>Note:</strong> {{ $grade->grade }}</p>
            <p class="card-text"><strong>Commentaire:</strong> {{ $grade->comment }}</p>
            <p class="card-text"><strong>Submission:</strong> {{ $grade->submission->id }}</p>
            <p class="card-text"><strong>Professeur:</strong> {{ $grade->teacher->username }}</p>
        </div>
    </div>
    <a href="{{ route('grades.index') }}" class="btn btn-primary mt-3">Retour à la liste</a>
</div>
@endsection
