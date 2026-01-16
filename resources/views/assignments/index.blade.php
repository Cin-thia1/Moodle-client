@extends('layouts.app')

@section('content')
<<<<<<< HEAD
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
=======
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

            <!-- Horizontal Navigation Tabs -->
            <div class="border-b border-gray-200">
                <div class="px-6 py-4 flex justify-center space-x-8 md:space-x-12 overflow-x-auto">
                    <a href="{{ route('assignments.index', ['view' => 'assignments']) }}"
                       class="pb-4 px-3 text-base font-medium transition-colors border-b-2 whitespace-nowrap
                              {{ (request('view') ?? 'assignments') === 'assignments' 
                                  ? 'text-indigo-600 border-indigo-600' 
                                  : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
                        <i class="fas fa-file-alt mr-2"></i> Énoncés
                    </a>

                    <a href="{{ route('assignments.index', ['view' => 'submissions']) }}"
                       class="pb-4 px-3 text-base font-medium transition-colors border-b-2 whitespace-nowrap
                              {{ request('view') === 'submissions' 
                                  ? 'text-indigo-600 border-indigo-600' 
                                  : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
>>>>>>> Evaluation
                        <i class="fas fa-paper-plane mr-2"></i> Soumissions
                    </a>
                </div>
            </div>

<<<<<<< HEAD
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
=======
            <!-- Back Button -->
            <div class="px-6 pt-6">
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>

            <!-- Main Content Area -->
            <div class="p-6">
                @if(request('view') === 'submissions')
                    <!-- Submissions Tab -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h3 class="text-xl font-bold text-gray-900">Liste des Soumissions</h3>
                    </div>

                    @if($submissions->isEmpty())
                        <div class="text-center py-16 bg-gray-50 rounded-xl">
                            <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Aucune soumission disponible</h4>
                            <p class="text-gray-500">Les soumissions apparaîtront ici une fois déposées par les étudiants.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Étudiant</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Évaluation</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($submissions as $submission)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-5 text-sm text-gray-900">{{ $submission->id }}</td>
                                            <td class="px-6 py-5 text-sm font-medium text-gray-900">{{ $submission->student->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-5 text-sm text-gray-900">{{ $submission->assignment->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-5 text-sm">
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                                    {{ in_array($submission->status, ['submitted', 'corrected']) 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-sm text-gray-900">
                                                {{ $submission->grade->grade ?? 'Non corrigée' }}
                                            </td>
                                            <td class="px-6 py-5 text-sm">
                                                <a href="{{ route('submissions.show', $submission->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                                    <i class="fas fa-eye mr-1"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                @else
                    <!-- Assignments Tab -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h3 class="text-xl font-bold text-gray-900">Liste des Énoncés</h3>

                        @if(auth()->user()?->hasRole('ROLE_TEACHER'))
                            <a href="{{ route('assignments.create') }}" 
                               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i> Nouvel Énoncé
                            </a>
                        @endif
                    </div>

                    @if($assignments->isEmpty())
                        <div class="text-center py-16 bg-gray-50 rounded-xl">
                            <i class="fas fa-folder-open text-6xl text-gray-400 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Aucun énoncé disponible</h4>
                            <p class="text-gray-500">Créez un nouvel énoncé pour commencer.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Module</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date Limite</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($assignments as $assignment)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-5 text-sm text-gray-900">{{ $assignment->id }}</td>
                                            <td class="px-6 py-5 text-sm font-medium text-gray-900">{{ $assignment->name }}</td>
                                            <td class="px-6 py-5 text-sm text-gray-900">{{ $assignment->module->name ?? '—' }}</td>
                                            <td class="px-6 py-5 text-sm text-gray-900">
                                                {{ $assignment->duedate ? \Carbon\Carbon::parse($assignment->duedate)->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-6 py-5 text-sm">
                                                <a href="{{ route('assignments.show', $assignment->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                                    <i class="fas fa-eye mr-1"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
>>>>>>> Evaluation
                @endif
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> Evaluation
