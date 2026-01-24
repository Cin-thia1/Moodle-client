@extends('layouts.app')

@section('title', 'Critères d\'évaluation - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📌 Critères d'évaluation</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($gradeItems as $item)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-indigo-600">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $item->item_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $item->item_type }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">
                        /{{ $item->grade_max }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <p class="text-xs text-gray-600">Moyenne</p>
                        <p class="text-xl font-bold text-indigo-600">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Médiane</p>
                        <p class="text-xl font-bold text-blue-600">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Min</p>
                        <p class="text-lg font-semibold text-orange-600">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Max</p>
                        <p class="text-lg font-semibold text-green-600">-</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">Aucun critère d'évaluation</p>
            </div>
            @endforelse
        </div>

        @can('manage_grades')
        <div class="mt-8 text-center">
            <button onclick="if(confirm('Synchroniser les critères depuis Moodle?')) document.getElementById('syncForm').submit()" 
                class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mx-auto">
                <i class="fas fa-sync"></i> Synchroniser depuis Moodle
            </button>
            <form id="syncForm" action="{{ route('grades.syncItems', $course) }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection

