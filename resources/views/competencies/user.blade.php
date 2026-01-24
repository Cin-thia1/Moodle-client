@extends('layouts.app')

@section('title', 'Mes compétences - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🎯 Mes compétences</h1>
            <p class="text-gray-600">{{ $course->fullname }}</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex gap-4">
                <a href="#all" class="px-4 py-2 border-b-2 border-indigo-600 text-indigo-600 font-semibold">
                    📋 Toutes les compétences
                </a>
                <a href="#completed" class="px-4 py-2 border-b-2 border-transparent text-gray-700 hover:text-indigo-600">
                    ✓ Maîtrisées
                </a>
            </nav>
        </div>

        <!-- All Competencies -->
        <div id="all" class="space-y-4 mb-12">
            @forelse(auth()->user()->competencies()->where('course_id', $course->id)->get() as $userCompetency)
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 {{ $userCompetency->proficiency ? 'border-green-500' : 'border-gray-300' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $userCompetency->competency->shortname }}</h3>
                            <p class="text-sm text-gray-600">{{ $userCompetency->competency->description }}</p>
                        </div>
                        <div class="text-right">
                            @if($userCompetency->proficiency)
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    ✓ Maîtrisée
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                    ⏳ En cours
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($userCompetency->grade)
                    <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-blue-900">
                            <strong>Note:</strong> {{ number_format($userCompetency->grade, 2) }}/100
                        </span>
                    </div>
                    @endif

                    @if($userCompetency->reviewed_at)
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-check-circle"></i> Validée le {{ $userCompetency->reviewed_at->format('d/m/Y') }}
                    </p>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <i class="fas fa-star text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-500">Aucune compétence assignée</p>
                </div>
            @endforelse
        </div>

        <!-- Completed Competencies -->
        <div id="completed" style="display:none;" class="space-y-4">
            @php
                $completed = auth()->user()->competencies()
                    ->where('course_id', $course->id)
                    ->where('proficiency', 1)
                    ->get();
            @endphp

            @if($completed->count() > 0)
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white mb-6">
                    <h2 class="text-2xl font-bold">🏆 {{ $completed->count() }} compétence(s) maîtrisée(s)</h2>
                </div>

                @foreach($completed as $userCompetency)
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $userCompetency->competency->shortname }}</h3>
                            <p class="text-sm text-gray-600">{{ $userCompetency->competency->description }}</p>
                        </div>
                        <div class="text-right">
                            @if($userCompetency->grade)
                                <p class="text-2xl font-bold text-green-600">{{ number_format($userCompetency->grade, 0) }}%</p>
                            @endif
                            <p class="text-xs text-gray-500">
                                ✓ {{ $userCompetency->reviewed_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-500">Aucune compétence maîtrisée pour le moment</p>
                </div>
            @endif
        </div>

        <script>
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelectorAll('[id^="all"], [id^="completed"]').forEach(el => {
                        el.style.display = el.id === link.href.split('#')[1] ? 'block' : 'none';
                    });
                    link.parentElement.querySelectorAll('a').forEach(a => {
                        a.classList.remove('border-indigo-600', 'text-indigo-600', 'font-semibold');
                        a.classList.add('border-transparent', 'text-gray-700', 'hover:text-indigo-600');
                    });
                    link.classList.add('border-indigo-600', 'text-indigo-600', 'font-semibold');
                    link.classList.remove('border-transparent', 'text-gray-700', 'hover:text-indigo-600');
                });
            });
        </script>
    </div>
</div>
@endsection
