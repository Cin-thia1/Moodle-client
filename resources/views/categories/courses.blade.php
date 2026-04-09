@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('categories.index') }}" class="text-blue-500 hover:text-blue-700 mb-4">← Retour aux catégories</a>

    <h1 class="text-3xl font-bold mb-6">Cours de {{ $category->name }}</h1>

    @if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($courses as $course)
        <div class="bg-white rounded shadow p-4">
            <h3 class="text-xl font-bold">{{ $course->fullname }}</h3>
            <p class="text-sm text-gray-600">{{ $course->shortname }}</p>
            <p class="text-sm mt-2">{{ Str::limit($course->summary, 100) }}</p>
            <a href="{{ route('courses.show', $course->id) }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                Voir cours
            </a>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-600 text-center py-8">Aucun cours dans cette catégorie</p>
    @endif
</div>
@endsection
