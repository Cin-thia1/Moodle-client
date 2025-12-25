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

                <!-- Colonne de gauche : Chronologie -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Chronologie des activités</h2>

                        <!-- Filtres -->
                        <div class="space-y-4 mb-8">
                            <div>
                                <label for="timeline-filter" class="text-sm font-medium text-gray-500">Échéance</label>
                                <select id="timeline-filter"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <optgroup label="Général">
                                        <option value="all">Toutes</option>
                                        <option value="overdue">En retard</option>
                                    </optgroup>
                                    <optgroup label="Prochains jours">
                                        <option value="7d">7 jours</option>
                                        <option value="30d">30 jours</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label for="timeline-sort" class="text-sm font-medium text-gray-500">Trier par</label>
                                <select id="timeline-sort"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="dates">Dates</option>
                                    <option value="courses">Cours</option>
                                </select>
                            </div>
                        </div>

                        <!-- État vide -->
                        <div
                            class="flex flex-col gap-4 justify-center items-center border-t border-gray-200 mt-8 py-12 text-gray-400 text-center">
                            <i class="far fa-calendar-check text-6xl text-gray-300"></i>
                            <span class="text-lg font-medium">Aucune activité à venir</span>
                            <p class="text-sm">Les devoirs et dates limites apparaîtront ici.</p>
                        </div>
                    </div>
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
                        <input type="checkbox" id="repeat_event" name="repeat_event" class="mr-2">
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

    <style>
        .calendar-wrapper {
            font-size: 0.875rem;
            /* text-sm */
        }

        .calendarmonth {
            table-layout: fixed;
            border: 1px solid #e5e7eb;
        }

        .calendarmonth th {
            padding: 0.5rem;
            background: #f9fafb;
        }

        .calendarmonth td {
            height: 100px;
            vertical-align: top;
            padding: 4px;
            border: 1px solid #e5e7eb;
            position: relative;
            background: white;
        }

        .calendarmonth td.weekend {
            background: #f9fafb;
        }

        .calendarmonth td.today {
            background: #eef2ff !important;
            border: 2px solid #6366f1;
        }

        .day-number-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: transparent;
            font-weight: bold;
            margin-bottom: 4px;
        }

        td.today .day-number-circle {
            background: #6366f1;
            color: white;
        }

        td.hasevent .day-number-circle {
            background: #e0e7ff;
        }

        .day-number {
            font-size: 1rem;
        }

        .event-list {
            list-style: none;
            padding: 0;
            margin: 4px 0 0 0;
            max-height: 70px;
            overflow-y: auto;
        }

        .event-item {
            font-size: 0.75rem;
            padding: 2px 4px;
            margin-bottom: 2px;
            border-radius: 4px;
            background: #c7d2fe;
            color: #4338ca;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .event-item.user {
            background: #d1fae5;
            color: #065f46;
        }
        .event-item:hover {
             background-color: #eef2ff !important;
    }

        /* Cercle de couleur selon type (comme Moodle) */
        .calendar-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            background: #6366f1;
        }

        .calendar_event_user {
            background: #10b981;
        }

        /* vert pour événements utilisateur */
    </style>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===================================
    // Éléments DOM
    // ===================================
    const calendarBody = document.getElementById('calendarBody');
    const currentMonthEl = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const prevMonthText = document.getElementById('prevMonthText');
    const nextMonthText = document.getElementById('nextMonthText');
    
    // Éléments du Modal
    const eventModal = document.getElementById('eventModal');
    const modalContent = document.getElementById('modalContent');
    const eventForm = document.getElementById('eventForm');
    const modalTitle = document.getElementById('modalTitle');
    const eventIdInput = document.getElementById('eventId');
    const sourceInput = document.getElementById('source');
    const deleteBtn = document.getElementById('deleteBtn');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    const durationNone = document.getElementById('duration_none');
    const durationUntil = document.getElementById('duration_until');
    const durationMinutesRadio = document.getElementById('duration_minutes'); // Le radio
    const endDateInput = document.getElementById('end_date');
    const durationMinutesInput = document.querySelector('input[type="number"][name="duration_minutes"]'); 
    
    const repeatEventCheckbox = document.getElementById('repeat_event');
    const repeatCountField = document.getElementById('repeat_count_field');
    const typeSelect = document.getElementById('type');

    // ===================================
    // État
    // ===================================
    let currentDate = new Date(); 
    let events = []; 

    // ===================================
    // Fonctions Utilitaires & UI
    // ===================================
    function toggleDurationFields() {
        endDateInput.style.display = durationUntil.checked ? 'block' : 'none';
        durationMinutesInput.style.display = durationMinutesRadio.checked ? 'block' : 'none';
    }

    function toggleRepeatField() {
        repeatCountField.style.display = repeatEventCheckbox.checked ? 'block' : 'none';
    }

    function toggleEventTypeFields() {
        const type = typeSelect.value;
        const courseField = document.getElementById('courseField');
        const categoryField = document.getElementById('categoryField');
        
        if (courseField) courseField.style.display = (type === 'cours') ? 'block' : 'none';
        if (categoryField) categoryField.style.display = (type === 'categorie') ? 'block' : 'none';
    }

    /**
     * Affiche une notification non-bloquante (utilitaire simple)
     * Note: safe (ne plante pas si env absente)
     */
    function showNotification(message, type = 'success') {
        try {
            const bg = type === 'success' ? 'bg-green-600' : 'bg-red-600';
            const node = document.createElement('div');
            node.className = `fixed top-6 right-6 ${bg} text-white px-4 py-2 rounded shadow-lg z-60`;
            node.textContent = message;
            document.body.appendChild(node);
            setTimeout(() => node.classList.add('opacity-100'), 10);
            setTimeout(() => { node.classList.remove('opacity-100'); node.remove(); }, 3000);
        } catch (e) {
            // Fallback silencieux
            console.info('Notification:', message, type);
        }
    }

    /**
     * Convertit un type Moodle ('user','course','category','site')
     * vers la valeur attendue par le formulaire local ('utilisateur','cours','categorie','site').
     */
    function mapMoodleTypeToLocal(moodleType) {
        if (!moodleType) return 'utilisateur';
        return ({
            'user': 'utilisateur',
            'course': 'cours',
            'category': 'categorie',
            'site': 'site'
        })[moodleType] || moodleType;
    }

    function toggleModal(show) {
        if (show) {
            eventModal.classList.remove('hidden');
            // Force l'affichage car votre HTML a opacity-0 et translate-y-4
            setTimeout(() => {
                modalContent.classList.remove('opacity-0', '-translate-y-4');
                modalContent.classList.add('opacity-100', 'translate-y-0');
            }, 10);
        } else {
            modalContent.classList.remove('opacity-100', 'translate-y-0');
            modalContent.classList.add('opacity-0', '-translate-y-4');
            setTimeout(() => {
                eventModal.classList.add('hidden');
                eventForm.reset();
                resetModal();
            }, 200);
        }
    }
    function openEventDetailModal(eventData, eventId, source) {
    // Remplir les champs
    document.getElementById('detailTitle').textContent = eventData.name || eventData.title;

    // Date et heure
    const start = new Date((eventData.timestart || Date.parse(eventData.date)) * 1000);
    const end = eventData.timeduration > 0 ? new Date(start.getTime() + eventData.timeduration * 1000) : null;

    const timeFormat = start.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }) + 
                       ', ' + start.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

    let timeText = timeFormat;
    if (end) {
        timeText += ' → ' + end.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }
    document.getElementById('detailTime').textContent = timeText;

    // Description
    document.getElementById('detailDescription').textContent = eventData.description || 'Je compose aujourd\'hui';

    // Emplacement
    document.getElementById('detailLocation').textContent = eventData.location || 'yaoundé';

    // Stocker les données pour édition/suppression (utilise fallback si nécessaire)
    document.getElementById('editEventBtn').dataset.eventId = eventId || eventData.id || eventData.eventid || eventData.eventId;
    document.getElementById('editEventBtn').dataset.source = source || eventData.source || 'local';
    document.getElementById('editEventBtn').dataset.eventData = JSON.stringify(eventData);

    const resolvedId = eventId || eventData.id || eventData.eventid || eventData.eventId;
    document.getElementById('deleteEventBtn').dataset.eventId = resolvedId;
    document.getElementById('deleteEventBtn').dataset.source = source || eventData.source || 'local';

    console.debug('Open detail modal for event', { resolvedId, source: source || eventData.source, eventData });

    // Afficher le modal
    document.getElementById('eventDetailModal').classList.remove('hidden');
}

    function resetModal() {
        modalTitle.textContent = 'Créer un nouvel événement';
        eventIdInput.value = '';
        sourceInput.value = 'local';
        deleteBtn.classList.add('hidden');
        eventForm.scrollTop = 0;
        toggleDurationFields();
        toggleRepeatField();
        toggleEventTypeFields();
    }

    // ===================================
    // Gestion des événements (API)
    // ===================================
    async function fetchEvents() {
        try {
            const response = await fetch('/events');
            if (!response.ok) throw new Error('Erreur réseau');
            events = await response.json();
            renderCalendar();
        } catch (err) {
            console.error(err);
        }
    }

    // ===================================
    // Rendu du calendrier
    // ===================================
    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const options = { year: 'numeric', month: 'long' };
        currentMonthEl.textContent = currentDate.toLocaleDateString('fr-FR', options).charAt(0).toUpperCase() + 
                                   currentDate.toLocaleDateString('fr-FR', options).slice(1);

        const prev = new Date(year, month - 1);
        const next = new Date(year, month + 1);
        prevMonthText.textContent = prev.toLocaleDateString('fr-FR', { month: 'long' });
        nextMonthText.textContent = next.toLocaleDateString('fr-FR', { month: 'long' });

        const firstDay = new Date(year, month, 1).getDay();
        const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1; 
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        const isToday = (d) => d.getDate() === today.getDate() && d.getMonth() === today.getMonth() && d.getFullYear() === today.getFullYear();

        calendarBody.innerHTML = '';
        let row = document.createElement('tr');
        let cellCount = 0;

        for (let i = 0; i < adjustedFirstDay; i++) {
            row.innerHTML += '<td class="dayblank">&nbsp;</td>';
            cellCount++;
        }

        for (let day = 1; day <= daysInMonth; day++) {
            if (cellCount === 7) {
                calendarBody.appendChild(row);
                row = document.createElement('tr');
                cellCount = 0;
            }

            const date = new Date(year, month, day);
            const dayEvents = events.filter(e => {
                const evDate = new Date((e.timestart ? e.timestart * 1000 : e.date));
                return evDate.getDate() === day && evDate.getMonth() === month && evDate.getFullYear() === year;
            });

            const hasEvent = dayEvents.length > 0;
            const weekend = date.getDay() === 0 || date.getDay() === 6;

            let cellClass = 'day text-start clickable';
            if (weekend) cellClass += ' weekend';
            if (hasEvent) cellClass += ' hasevent';
            if (isToday(date)) cellClass += ' today';

            // Dans la boucle des jours, à l'intérieur de renderCalendar()
let eventsHtml = '';
if (hasEvent) {
    eventsHtml = '<ul class="event-list">';
    dayEvents.forEach(ev => {
        const typeClass = ev.eventtype === 'user' ? 'user' : '';
        const typeIcon = ev.eventtype === 'user' ? 'fa-user' : 'fa-book'; // Icône selon type
        eventsHtml += `
            <li class="event-item ${typeClass} cursor-pointer hover:bg-indigo-200 transition-colors p-1 rounded"
                data-event-id="${ev.id}"
                data-event-source="${ev.source || 'local'}"
                data-event-data='${JSON.stringify(ev).replace(/'/g, "&#39;")}'>
                <span class="calendar-circle calendar_event_${ev.eventtype || 'user'}"></span>
                <span class="eventname text-xs">${ev.name || ev.title}</span>
            </li>`;
    });
    eventsHtml += '</ul>';
}

            row.innerHTML += `
            <td class="${cellClass}" data-day="${day}">
                <div class="text-center md:text-left">
                    <span class="day-number-circle">
                        <span class="day-number">${day}</span>
                    </span>
                </div>
                ${eventsHtml}
            </td>`;
            cellCount++;
        }

        while (cellCount < 7) {
            row.innerHTML += '<td class="dayblank">&nbsp;</td>';
            cellCount++;
        }
        calendarBody.appendChild(row);
    }
    // Clique sur un événement → ouvre le modal de détail
calendarBody.addEventListener('click', function(e) {
    const eventItem = e.target.closest('.event-item');
    if (!eventItem) return;

    const eventData = JSON.parse(eventItem.getAttribute('data-event-data'));
    const eventId = eventItem.getAttribute('data-event-id');
    const source = eventItem.getAttribute('data-event-source');

    // Ouvre un modal de détail inspiré Moodle
    openEventDetailModal(eventData, eventId, source);
});

    // ===================================
    // Écouteurs d'événements (Listeners)
    // ===================================
    
    // Boutons d'ouverture/fermeture MODAL (CORRIGÉ)
    if (openModalBtn) openModalBtn.addEventListener('click', () => toggleModal(true));
    if (closeModalBtn) closeModalBtn.addEventListener('click', () => toggleModal(false));

    // Navigation calendrier
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Formulaire Modal (Toggle champs)
    durationNone.addEventListener('change', toggleDurationFields);
    durationUntil.addEventListener('change', toggleDurationFields);
    durationMinutesRadio.addEventListener('change', toggleDurationFields);
    repeatEventCheckbox.addEventListener('change', toggleRepeatField);
    typeSelect.addEventListener('change', toggleEventTypeFields);

    // Clic sur un événement pour modifier
    calendarBody.addEventListener('click', async (e) => {
        const item = e.target.closest('.event-item');
        if (item) {
            const id = item.dataset.eventId;
            const source = item.dataset.source;
            try {
                const response = await fetch(`/events/${id}?source=${source}`);
                const eventData = await response.json();
                openEditModal(eventData);
            } catch (err) {
                console.error(err);
            }
        }
    });

    async function openEditModal(eventData) {
        modalTitle.textContent = 'Modifier l\'événement';
        eventIdInput.value = eventData.id;
        sourceInput.value = eventData.source || 'local';
        document.getElementById('title').value = eventData.name || eventData.title;
        
        const timestamp = eventData.timestart || Date.parse(eventData.date) / 1000;
        const date = new Date(timestamp * 1000);
        document.getElementById('date').value = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
        
        // Si l'événement vient de Moodle, convertir le type pour la validation côté serveur
        typeSelect.value = mapMoodleTypeToLocal(eventData.eventtype || eventData.type);
        document.getElementById('description').value = eventData.description || '';
        document.getElementById('location').value = eventData.location || '';
        const timeduration = parseInt(eventData.timeduration) || 0;
        if (timeduration === 0) {
            durationNone.checked = true;
        } else if (eventData.end_date || timeduration > 3600 * 24) { 
            durationUntil.checked = true;
            const endDate = new Date(date.getTime() + timeduration * 1000);
            endDateInput.value = new Date(endDate.getTime() - (endDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
        } else {
            durationMinutesRadio.checked = true;
            durationMinutesInput.value = timeduration / 60;
        }
        
        const repeats = eventData.repeats || 0;
        repeatEventCheckbox.checked = repeats > 0;
        document.getElementById('repeat_count').value = repeats;

        toggleDurationFields();
        toggleRepeatField();
        toggleEventTypeFields();

        if (eventData.courseid) document.getElementById('course_id').value = eventData.courseid;
        if (eventData.categoryid) document.getElementById('category_id').value = eventData.categoryid;

        deleteBtn.classList.remove('hidden');
        toggleModal(true);
    }

    eventForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = eventIdInput.value;
        const url = id ? `/events/${id}` : '/events';

        // For updates, use method spoofing so Laravel can parse multipart data reliably
        if (id) {
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                }
            });

            let data = {};
            try { data = await response.json(); } catch(e) { /* ignore parse errors */ }

            if (!response.ok) {
                console.error('Submit failed', response.status, data);
                // Si erreurs de validation, afficher le premier message utile
                if (response.status === 422 && data.errors) {
                    const firstField = Object.keys(data.errors)[0];
                    const firstMsg = data.errors[firstField] && data.errors[firstField][0];
                    try { showNotification(firstMsg || 'Erreur de validation', 'error'); } catch(e) { alert(firstMsg || 'Erreur de validation'); }
                } else {
                    try { showNotification(data.error || data.message || 'Erreur lors de l\'enregistrement', 'error'); } catch(e) { alert(data.error || data.message || 'Erreur lors de l\'enregistrement'); }
                }
                return;
            }

            try { showNotification(data.message || 'Enregistré !'); } catch(e) { console.info('Enregistré'); }
            toggleModal(false);
            fetchEvents();
        } catch (error) {
            console.error(error);
            try { showNotification('Erreur lors de l\'enregistrement', 'error'); } catch(e) { alert('Erreur lors de l\'enregistrement'); }
        }
    });

    deleteBtn.addEventListener('click', async () => {
        if (confirm('Supprimer cet événement ?')) {
            const id = eventIdInput.value;
            const source = sourceInput.value;
            try {
                await fetch(`/events/${id}?source=${source}`, { 
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': new FormData(eventForm).get('_token') }
                });
                toggleModal(false);
                fetchEvents();
            } catch (error) {
                console.error(error);
            }
        }
    });

    fetchEvents();
    // Fermer le modal de détail
document.getElementById('closeDetailModal').addEventListener('click', () => {
    document.getElementById('eventDetailModal').classList.add('hidden');
});

// Cliquer dehors → fermer
document.getElementById('eventDetailModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('eventDetailModal')) {
        document.getElementById('eventDetailModal').classList.add('hidden');
    }
});

// Bouton Modifier → ouvre le modal de création en mode édition
document.getElementById('editEventBtn').addEventListener('click', async function() {
    const btn = this;
    const eventId = btn.dataset.eventId;
    const source = btn.dataset.source || 'local';
    let eventData = null;

    // Try parse dataset.eventData safely
    if (btn.dataset.eventData) {
        try {
            eventData = JSON.parse(btn.dataset.eventData);
        } catch (err) {
            console.warn('editEventBtn: failed to parse dataset.eventData, will fetch from server', err);
        }
    }

    // If no eventData from dataset, fetch from server as fallback
    if (!eventData) {
        if (!eventId) {
            console.error('editEventBtn: missing event id and event data', btn.dataset);
            try { showNotification('Impossible de charger l\'événement pour édition', 'error'); } catch(e) { alert('Impossible de charger l\'événement pour édition'); }
            return;
        }

        try {
            btn.disabled = true;
            const resp = await fetch(`/events/${eventId}?source=${source}`, { headers: { 'Accept': 'application/json' } });
            if (!resp.ok) {
                const text = await resp.text();
                console.error('Failed to fetch event for edit', resp.status, text);
                try { showNotification('Impossible de récupérer l\'événement', 'error'); } catch(e) { alert('Impossible de récupérer l\'événement'); }
                return;
            }
            eventData = await resp.json();
        } catch (err) {
            console.error('Error fetching event for edit', err);
            try { showNotification('Erreur réseau lors du chargement de l\'événement', 'error'); } catch(e) { alert('Erreur réseau lors du chargement de l\'événement'); }
            return;
        } finally {
            btn.disabled = false;
        }
    }

    try {
        openEditModal(eventData);
        toggleModal(true);
        document.getElementById('eventDetailModal').classList.add('hidden');
    } catch (err) {
        console.error('Error opening edit modal', err, { eventData });
        try { showNotification('Erreur lors de l\'ouverture du formulaire d\'édition', 'error'); } catch(e) { alert('Erreur lors de l\'ouverture du formulaire d\'édition'); }
    }
});

// Bouton Supprimer
document.getElementById('deleteEventBtn').addEventListener('click', function() {
    if (!confirm('Supprimer cet événement ?')) return;

    const eventId = this.dataset.eventId;
    const source = this.dataset.source;

    if (!eventId) {
        console.error('Attempt to delete event but eventId is missing', { dataset: this.dataset });
        try { showNotification('Impossible de supprimer : identifiant manquant', 'error'); } catch(e) { alert('Impossible de supprimer : identifiant manquant'); }
        return;
    }

    fetch(`/events/${eventId}?source=${source}`, {
        method: 'DELETE',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        if (!response.ok) {
            const text = await response.text();
            console.error('Delete failed', response.status, text);
            throw new Error(text || 'Erreur lors de la suppression');
        }
        try { showNotification('Événement supprimé !'); } catch(e) { console.info('Notification: événement supprimé'); }
        document.getElementById('eventDetailModal').classList.add('hidden');
        fetchEvents(); // Rafraîchir le calendrier
    })
    .catch(err => {
        console.error('Delete error:', err);
        try { showNotification('Erreur lors de la suppression', 'error'); } catch(e) { alert('Erreur lors de la suppression'); }
    });
});
});

</script>