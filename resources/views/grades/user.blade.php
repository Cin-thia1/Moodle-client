@extends('layouts.app')

@section('title', 'Mes notes - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📈 Vos notes</h1>
            <p class="text-gray-600">{{ $course->fullname }}</p>
        </div>

        @php
            $totalGrade = 0;
            $totalMax = 0;
        @endphp

        <div class="space-y-4">
            @forelse(auth()->user()->grades()->where('course_id', $course->id)->get() as $grade)
                @php
                    $totalGrade += $grade->final_grade ?? 0;
                    $totalMax += $grade->gradeItem->grade_max ?? 0;
                @endphp
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-indigo-600">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $grade->gradeItem->item_name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-600">{{ $grade->gradeItem->item_type ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ $grade->final_grade ?? '-' }}
                                <span class="text-gray-400 text-lg">/{{ $grade->gradeItem->grade_max ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    @if($grade->final_grade !== null && $grade->gradeItem)
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-indigo-600 h-2 transition-all"
                                style="width: {{ min(($grade->final_grade / $grade->gradeItem->grade_max) * 100, 100) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            {{ number_format(($grade->final_grade / $grade->gradeItem->grade_max) * 100, 0) }}%
                        </p>
                    @endif

                    @if($grade->feedback)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-blue-900">
                                <i class="fas fa-comment mr-2"></i>
                                <strong>Commentaire:</strong> {{ $grade->feedback }}
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-500">Aucune note disponible</p>
                </div>
            @endforelse
        </div>

        @if($totalMax > 0)
        <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-3">📊 Résumé général</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-indigo-100 text-sm">Total obtenu</p>
                    <p class="text-3xl font-bold">{{ number_format($totalGrade, 2) }}</p>
                </div>
                <div>
                    <p class="text-indigo-100 text-sm">Total possible</p>
                    <p class="text-3xl font-bold">{{ number_format($totalMax, 2) }}</p>
                </div>
                <div>
                    <p class="text-indigo-100 text-sm">Pourcentage</p>
                    <p class="text-3xl font-bold">{{ number_format(($totalGrade / $totalMax) * 100, 1) }}%</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
