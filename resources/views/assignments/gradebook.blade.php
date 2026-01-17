@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b">
      <h1 class="text-lg font-bold text-gray-800">
        Carnet de notes — {{ $course->fullname }}
      </h1>
      <p class="text-xs text-gray-500 mt-1">
        Vue “front only” (notes mock).
      </p>
    </div>

    <div class="overflow-x-auto p-4">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-4 py-3 font-semibold text-gray-600">Élève</th>
            @foreach($assignments as $a)
              <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">
                {{ $a->name }}
              </th>
            @endforeach
            <th class="text-left px-4 py-3 font-semibold text-gray-600">Moyenne</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @foreach($students as $s)
            @php
              $sum = 0; $count = 0;
            @endphp
            <tr class="hover:bg-gray-50 transition">
              <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                {{ $s->name }}
              </td>

              @foreach($assignments as $a)
                @php
                  $g = $matrix[$s->id][$a->id] ?? null;
                  if($g !== null){ $sum += $g; $count++; }
                @endphp
                <td class="px-4 py-3">
                  {{ $g ?? '—' }}
                </td>
              @endforeach

              <td class="px-4 py-3 font-semibold">
                {{ $count ? round($sum / $count, 2) : '—' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}"
         class="text-blue-600 hover:underline text-sm">
        ← Retour aux devoirs
      </a>
    </div>
  </div>
</div>
@endsection
