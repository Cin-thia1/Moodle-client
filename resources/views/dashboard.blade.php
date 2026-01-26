@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <!-- En-tête de la page -->
            <header class="mb-10">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
                    Tableau de bord
                </h1>
                <p class="mt-2 text-lg text-gray-600">Votre centre de contrôle pour les activités et événements.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
<!-- Chronologie des activités -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Chronologie des activités</h2>

    <!-- Filtres -->
    <div class="flex space-x-4 mb-6">
        <div>
            <label class="block text-sm text-gray-700">Échéance</label>
            <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option>Toutes</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-700">Trier par</label>
            <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option>Dates</option>
            </select>
        </div>
    </div>

    @if($assignments->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-calendar-times text-5xl mb-3"></i>
            <p class="text-lg font-medium">Aucune activité à venir</p>
            <p class="text-sm mt-2">Les devoirs et dates limites apparaîtront ici.</p>
        </div>
    @else
        <ul class="space-y-4">
            @foreach($assignments as $assignment)
                <li class="border-l-4 border-indigo-500 pl-4 py-3 bg-indigo-50 rounded-r">
                    <p class="font-medium text-indigo-800">
                        {{ $assignment->name }}
                    </p>

                    <p class="text-sm text-gray-600 mt-1">
                        Échéance :
                        {{ $assignment->duedate ? $assignment->duedate->format('d/m/Y H:i') : 'Non définie' }}
                    </p>

                    <!-- ✅ Solution 1 : passer par section -> course -->
                    <p class="text-xs text-gray-500 mt-1">
                        Cours : {{ $assignment->section?->course?->fullname ?? 'Non spécifié' }}
                    </p>

                    <a href="{{ route('assignments.show', $assignment->id) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">
                        Voir le devoir →
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>


                <!-- Calendrier -->
                <!-- Colonne de droite : Calendrier -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Calendrier</h2>
                        <div class="flex items-center gap-4">
                            <select id="course-filter"
                                class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all">Tous les cours</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                                @endforeach
                            </select>
                            <button id="openModalBtn"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                <i class="fas fa-plus"></i>
                                Nouvel événement
                            </button>
                        </div>
                    </div>


                    <div class="calendar-wrapper select-none">
                        <!-- Navigation mois -->
                        <div class="header d-flex flex-wrap p-3 bg-gray-50 border border-gray-200 rounded-t-lg">
                            <div class="controls flex items-center justify-between w-full">
                                <button id="prevMonth" class="arrow_link previous p-2 hover:bg-gray-200 rounded">
                                    <span class="arrow">‹</span>
                                    <span class="arrow_text" id="prevMonthText"></span>
                                </button>
                                <h2 class="current text-lg font-bold" id="currentMonth"></h2>
                                <button id="nextMonth" class="arrow_link next p-2 hover:bg-gray-200 rounded">
                                    <span class="arrow_text" id="nextMonthText"></span>
                                    <span class="arrow">›</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tableau du mois -->
                        <table class="calendarmonth calendartable w-full border-collapse">
                            <thead>
                                <tr class="text-center text-sm font-medium text-gray-600">
                                    <th>Lu</th>
                                    <th>Ma</th>
                                    <th>Me</th>
                                    <th>Je</th>
                                    <th>Ve</th>
                                    <th class="weekend">Sa</th>
                                    <th class="weekend">Di</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody">
                                <!-- Généré par JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal pour créer/modifier un événement -->
<!-- Modal pour créer/modifier un événement -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4 overflow-y-auto">
    <div id="modalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col transform transition-all opacity-0 -translate-y-4">
        <!-- En-tête -->
        <div class="flex justify-between items-center p-6 pb-4 border-b">
            <h2 id="modalTitle" class="text-xl font-bold text-gray-900">Créer un nouvel événement</h2>
            <button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 transition-colors">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>

        <!-- Corps du formulaire avec scroll -->
        <div class="overflow-y-auto px-6 py-4 flex-1 text-sm"> <!-- ← Police réduite + scroll -->
            <form id="eventForm" class="space-y-4">
                @csrf
                <input type="hidden" id="eventId" name="id">
                <input type="hidden" id="source" name="source" value="local">

                <div>
                    <label for="title" class="block font-medium text-gray-700">Nom de l'événement <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="date" class="block font-medium text-gray-700">Date & Heure <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="date" name="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="type" class="block font-medium text-gray-700">Type</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="utilisateur" selected>Utilisateur</option>
                        <option value="cours">Cours</option>
                        <option value="categorie">Catégorie</option>
                        <option value="site">Site</option>
                    </select>
                </div>

                <div id="courseField" style="display: none;">
                    <label for="course_id" class="block font-medium text-gray-700">Cours</label>
                    <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="categoryField" style="display: none;">
                    <label for="category_id" class="block font-medium text-gray-700">Catégorie</label>
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="location" class="block font-medium text-gray-700">Emplacement</label>
                    <input type="text" id="location" name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Durée</label>
                    <div class="space-y-2 ml-4">
                        <div class="flex items-center">
                            <input type="radio" id="duration_none" name="duration_type" value="none" checked class="mr-2">
                            <label for="duration_none">Sans durée</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="duration_until" name="duration_type" value="until" class="mr-2">
                            <label for="duration_until">Jusqu'au</label>
                            <input type="datetime-local" id="end_date" name="end_date" class="ml-3 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" style="display: none;">
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="duration_minutes" name="duration_type" value="minutes" class="mr-2">
                            <label for="duration_minutes">Durée en minutes</label>
                            <input type="number" id="duration_minutes" name="duration_minutes" min="1" class="ml-3 w-24 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" style="display: none;">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center">
                        <input type="checkbox" id="repeat_event" name="repeat_event" class="mr-2" value="1">
                        <label for="repeat_event" class="font-medium text-gray-700">Répéter cet événement</label>
                    </div>
                    <div id="repeat_count_field" style="display: none;" class="mt-2 ml-6">
                        <label for="repeat_count" class="block font-medium text-gray-700">Nombre de répétitions hebdomadaires</label>
                        <input type="number" id="repeat_count" name="repeat_count" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="description" class="block font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
            </form>
        </div>

        <!-- Pied avec boutons -->
        <div class="p-6 pt-4 border-t flex justify-end space-x-4">
            <button type="button" id="deleteBtn" class="hidden bg-red-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-red-700 transition-colors">Supprimer</button>
            <button type="submit" form="eventForm" class="bg-indigo-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                Enregistrer
            </button>
        </div>
    </div>
</div>

<!-- Modal de détail / modification d'un événement (style Moodle) -->
<div id="eventDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <div class="flex justify-between items-center p-4 border-b bg-gray-50 rounded-t-lg">
            <h3 id="detailTitle" class="text-lg font-bold text-gray-900"></h3>
            <button id="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 text-sm">
            <div class="flex items-center text-gray-700">
                <i class="fas fa-clock mr-3 text-gray-500"></i>
                <span id="detailTime"></span>
            </div>

            <div class="flex items-center text-gray-700">
                <i class="fas fa-calendar mr-3 text-gray-500"></i>
                <span id="detailType">Événement privé</span>
            </div>

            <div class="flex items-start text-gray-700">
                <i class="fas fa-align-left mr-3 text-gray-500 mt-1"></i>
                <span id="detailDescription" class="whitespace-pre-wrap"></span>
            </div>

            <div class="flex items-center text-gray-700">
                <i class="fas fa-map-marker-alt mr-3 text-gray-500"></i>
                <span id="detailLocation"></span>
            </div>
        </div>

        <div class="p-4 border-t flex justify-end space-x-3">
            <button id="deleteEventBtn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Supprimer
            </button>
            <button id="editEventBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Modifier
            </button>
        </div>
    </div>
</div>
    </div>


@endsection

@push('styles')
@vite(['resources/css/dashboard.css'])
@endpush

@push('scripts')
@vite(['resources/js/dashboard.js'])
@endpush




    
 