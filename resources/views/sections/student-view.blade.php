@extends('layouts.app')

@section('title', 'Sections - ' . $course->fullname)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left"></i> Retour au cours
            </a>
            <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-stream text-purple-500"></i> Sections
            </h1>
            <p class="text-gray-600 mt-1">Contenu du cours {{ $course->fullname }}</p>
        </div>

        <!-- Sections List -->
        @if ($sections->count())
            <div class="space-y-4">
                @foreach ($sections as $section)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $section->name }}</h3>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ $section->modules->count() }} module(s)
                            </p>
                            @if ($section->modules->count())
                                <div class="mt-3 space-y-2">
                                    @foreach ($section->modules as $module)
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <i class="fas fa-cube text-indigo-600"></i>
                                            {{ $module->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">Aucune section disponible pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection
