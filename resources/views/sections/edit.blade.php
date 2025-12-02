@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editer une Section</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sections.update', $section) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $section->name }}" required>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Cours</label>
            <select name="course_id" id="course_id" class="form-control" required>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $section->course_id ? 'selected' : '' }}>
                        {{ $course->fullname }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
@endsection
