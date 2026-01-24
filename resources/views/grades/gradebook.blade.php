@extends('layouts.app')

@section('title', 'Carnet de notes - ' . $course->fullname)

@section('content')
<div class="bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📊 Carnet de notes</h1>
                <p class="text-gray-600 mt-1">{{ $course->fullname }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-x-auto mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 min-w-200">Étudiant</th>
                        @foreach($gradeItems as $item)
                        <th class="px-3 py-3 text-center font-semibold text-gray-700 min-w-100">
                            <div class="flex flex-col">
                                <span class="text-xs">{{ $item->item_name }}</span>
                                <span class="text-xs text-gray-500">/{{ $item->grade_max }}</span>
                            </div>
                        </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-semibold text-gray-700 min-w-80">Moyenne</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $participant)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $participant->user->name }}
                        </td>
                        @php
                            $total = 0;
                            $count = 0;
                        @endphp
                        @foreach($gradeItems as $item)
                            @php
                                $grade = $participant->user->userGrades()
                                    ->where('grade_item_id', $item->id)
                                    ->first();
                                $score = $grade ? $grade->final_grade : null;
                                if ($score !== null) {
                                    $total += $score;
                                    $count++;
                                }
                            @endphp
                            <td class="px-3 py-3 text-center">
                                @if($score !== null)
                                    <span class="px-2 py-1 rounded {{ $score >= ($item->grade_max * 0.7) ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $score }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-3 py-3 text-center font-semibold">
                            @if($count > 0)
                                {{ number_format($total / $count, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($gradeItems) + 2 }}" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-book text-4xl text-gray-300 mb-3 block"></i>
                            Aucun étudiant
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @can('manage_grades')
        <div class="text-center">
            <button onclick="if(confirm('Synchroniser les notes depuis Moodle?')) document.getElementById('syncForm').submit()" 
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

