@extends('layouts.app')

@section('content')
<div class="container bg-white p-6 rounded">
    <!-- Bouton Retour -->
    <div class="p-6">
                    <a href="{{ route('sections.index') }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
    <h1>Details de la section</h1>
    <p><strong>ID:</strong> {{ $section->id }}</p>
    <p><strong>Nom:</strong> {{ $section->name }}</p>
    <p><strong>Cours:</strong> {{ $section->course->fullname }}</p>

    <h3>Modules</h3>
    <ul>
        @foreach ($section->modules as $module)
            <li>{{ $module->name }}</li>
        @endforeach
    </ul>

    <!-- <a href="{{ route('sections.index') }}" class="btn btn-secondary">Back</a> -->
</div>
@endsection
