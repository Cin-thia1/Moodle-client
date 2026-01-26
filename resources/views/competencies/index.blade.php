@extends('layouts.app')

@section('title', $course ? 'Compétences - ' . $course->fullname : 'Compétences')

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                @if($course)
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-star text-orange-500"></i> Compétences du cours
                </h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
                @else
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-star text-orange-500"></i> Toutes les compétences
                </h1>
                @endif
            </div>
            @can('manage_competencies')
            <a href="{{ route('competencies.create', ['course_id' => $course?->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Ajouter
            </a>
            @endcan
        </div>

        @if($course)
            {{-- Section Compétences associées (Pastilles) --}}
            <div class="mb-10 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-link text-indigo-500"></i> Compétences associées au cours
                </h2>
                
                @if(isset($courseCompetencies) && $courseCompetencies->count() > 0)
                    <div class="flex flex-wrap gap-3">
                        @foreach($courseCompetencies as $competency)
                            <div class="inline-flex items-center bg-indigo-50 text-indigo-800 px-4 py-2 rounded-full text-sm font-medium border border-indigo-200 shadow-sm transition hover:bg-indigo-100">
                                <span class="mr-2">{{ $competency->shortname }}</span>
                                @can('manage_competencies')
                                <form action="{{ route('competencies.detach', [$course, $competency]) }}" method="POST" class="inline-flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-indigo-400 hover:text-red-600 focus:outline-none transition-colors" title="Retirer du cours">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> Aucune compétence n'est actuellement associée à ce cours.
                    </p>
                @endif
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-list text-gray-500"></i> Compétences disponibles
            </h2>
        @endif

        {{-- Liste des compétences (Disponibles pour le cours OU Toutes si hors cours) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $displayCompetencies = $course ? ($availableCompetencies ?? []) : ($competencies ?? []);
            @endphp

            @forelse($displayCompetencies as $competency)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $competency->shortname }}</h3>
                        <p class="text-xs text-gray-500">ID: {{ $competency->idnumber }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $competency->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $competency->status ? 'Actif' : 'Inactif' }}
                        </span>

                        @if($course)
                            @can('manage_competencies')
                            <form action="{{ route('competencies.attach', [$course, $competency]) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:text-indigo-800 transition transform hover:scale-110" title="Associer au cours">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </button>
                            </form>
                            @endcan
                        @endif
                    </div>
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

        @if($course)
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
        @endif
    </div>
</div>
@endsection
