@extends('layouts.app')

@section('title', 'Compétences - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">🎯 Compétences du cours</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
            @can('manage_competencies')
            <a href="{{ route('competencies.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Ajouter
            </a>
            @endcan
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($competencies as $competency)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $competency->shortname }}</h3>
                        <p class="text-xs text-gray-500">ID: {{ $competency->idnumber }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $competency->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $competency->status ? 'Actif' : 'Inactif' }}
                    </span>
                </div>

                <p class="text-sm text-gray-700 mb-4">{{ $competency->description }}</p>

                @php
                    $userCompetencies = auth()->user()->competencies()
                        ->where('competency_id', $competency->id)
                        ->get();
                    $completedCount = $userCompetencies->where('proficiency', 1)->count();
                    $totalCount = $userCompetencies->count();
                @endphp

                @if($totalCount > 0)
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Maîtrise</span>
                        <span>{{ $completedCount }}/{{ $totalCount }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-green-500 h-2 transition-all"
                            style="width: {{ ($completedCount / $totalCount) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endif

                @can('manage_competencies')
                <div class="flex gap-2">
                    <a href="{{ route('competencies.edit', $competency) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                </div>
                @endcan
            </div>
            @empty
            <div class="col-span-2 bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-star text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">Aucune compétence définie</p>
            </div>
            @endforelse
        </div>

        @can('manage_competencies')
        <div class="mt-8 text-center">
            <button onclick="if(confirm('Synchroniser les compétences depuis Moodle?')) document.getElementById('syncForm').submit()" 
                class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mx-auto">
                <i class="fas fa-sync"></i> Synchroniser depuis Moodle
            </button>
            <form id="syncForm" action="{{ route('competencies.syncCourse', $course) }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
