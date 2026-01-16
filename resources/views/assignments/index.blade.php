@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg">
            <!-- Barre de navigation horizontale -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-center space-x-6">
                    <a href="{{ route('assignments.index', ['view' => 'assignments']) }}"
                       class="text-lg font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition 
                              {{ request('view') === 'assignments' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                        <i class="fas fa-file-alt mr-2"></i> Énoncés
                    </a>
                    <a href="{{ route('assignments.index', ['view' => 'submissions']) }}"
                       class="text-lg font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition 
                              {{ request('view') === 'submissions' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                        <i class="fas fa-paper-plane mr-2"></i> Soumissions
                    </a>
                </div>
            </div>

            <!-- Bouton Retour -->
            <div class="p-6">
                    <a href="{{ url()->previous() }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>

            <!-- Contenu principal -->
            <div class="p-6">
                @if(request('view') === 'submissions')
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Liste des Soumissions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Étudiant</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Évaluation</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Statut</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Note</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $submission->id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $submission->student->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $submission->assignment->name }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-2 py-1 text-xs font-medium rounded-lg
                                                {{ $submission->status === 'submitted'|| $submission->status === 'corrected' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ ucfirst($submission->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $submission->grade->grade ?? 'Non corrigée' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">
                                            <a href="{{ route('submissions.show', $submission->id) }}" 
                                               class="text-blue-500 hover:text-blue-700 font-medium">
                                                <i class="fas fa-eye mr-1"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    @if(auth()->user()->hasRole('ROLE_TEACHER'))
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Liste des Énoncés</h3>
                            <a href="{{ route('assignments.create') }}" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-plus mr-1"></i> Nouvel Énoncé
                            </a>
                        </div>
                    @endif


                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nom</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Module</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date Limite</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignments as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $assignment->id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $assignment->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $assignment->module->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ \Carbon\Carbon::parse($assignment->duedate)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800">
                                            <a href="{{ route('assignments.show', $assignment->id) }}" 
                                               class="text-blue-500 hover:text-blue-700 font-medium">
                                                <i class="fas fa-eye mr-1"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
