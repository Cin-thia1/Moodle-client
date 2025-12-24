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

        <!-- Modal pour créer un événement -->
        <div id="eventModal"
            class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
            <div id="modalContent"
                class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all opacity-0 -translate-y-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Créer un nouvel événement</h2>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 transition-colors"><i
                            class="fas fa-times fa-lg"></i></button>
                </div>
                <form id="eventForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                        <input type="text" id="title" name="title" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date & Heure</label>
                        <input type="datetime-local" id="date" name="date" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="utilisateur" selected>Utilisateur</option>
                            <option value="cours">Cours</option>
                            <option value="categorie">Catégorie</option>
                            <option value="site">Site</option>
                        </select>
                    </div>
                    <div id="courseField" style="display: none;">
                        <label for="course_id" class="block text-sm font-medium text-gray-700">Cours</label>
                        <select id="course_id" name="course_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="categoryField" style="display: none;">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select id="category_id" name="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 flex justify-end">
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                            Enregistrer l'événement
                        </button>
                    </div>
                </form>
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
        }

        .event-item.user {
            background: #d1fae5;
            color: #065f46;
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
        // Éléments DOM
        const calendarBody = document.getElementById('calendarBody');
        const currentMonthEl = document.getElementById('currentMonth');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const prevMonthText = document.getElementById('prevMonthText');
        const nextMonthText = document.getElementById('nextMonthText');

        // État
        let currentDate = new Date(); // Date courante du calendrier
        let events = []; // Tableau d'événements [{id, name/title, date/timestart, eventtype, ...}]

        // ===================================
        // Récupération des événements
        // ===================================
        async function fetchEvents() {
            try {
                const response = await fetch('/events');
                if (!response.ok) throw new Error('Erreur réseau');
                events = await response.json();
                renderCalendar();
            } catch (err) {
                console.error(err);
                showNotification("Impossible de charger les événements", 'error');
            }
        }

        // ===================================
        // Rendu du calendrier
        // ===================================
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Mise à jour du titre
            const options = {
                year: 'numeric',
                month: 'long'
            };
            currentMonthEl.textContent = currentDate.toLocaleDateString('fr-FR', options).charAt(0)
            .toUpperCase() + currentDate.toLocaleDateString('fr-FR', options).slice(1);

            // Texte des flèches
            const prev = new Date(year, month - 1);
            const next = new Date(year, month + 1);
            prevMonthText.textContent = prev.toLocaleDateString('fr-FR', {
                month: 'long'
            });
            nextMonthText.textContent = next.toLocaleDateString('fr-FR', {
                month: 'long'
            });

            // Premier jour du mois (lundi = 1, dimanche = 0)
            const firstDay = new Date(year, month, 1).getDay();
            const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1; 
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Aujourd'hui
            const today = new Date();
            const isToday = (d) => d.getDate() === today.getDate() && d.getMonth() === today.getMonth() && d
                .getFullYear() === today.getFullYear();

            // Nettoyage
            calendarBody.innerHTML = '';

            let row = document.createElement('tr');
            let cellCount = 0;

            // Cases vides avant le 1er jour
            for (let i = 0; i < adjustedFirstDay; i++) {
                row.innerHTML += '<td class="dayblank">&nbsp;</td>';
                cellCount++;
            }

            // Jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                if (cellCount === 7) {
                    calendarBody.appendChild(row);
                    row = document.createElement('tr');
                    cellCount = 0;
                }

                const date = new Date(year, month, day);
                const dayEvents = events.filter(e => {
                    const evDate = new Date((e.timestart ? e.timestart * 1000 : e.date));
                    return evDate.getDate() === day && evDate.getMonth() === month && evDate
                        .getFullYear() === year;
                });

                const hasEvent = dayEvents.length > 0;
                const weekend = date.getDay() === 0 || date.getDay() === 6;

                let cellClass = 'day text-start clickable';
                if (weekend) cellClass += ' weekend';
                if (hasEvent) cellClass += ' hasevent';
                if (isToday(date)) cellClass += ' today';

                let eventsHtml = '';
                if (hasEvent) {
                    eventsHtml = '<ul class="event-list">';
                    dayEvents.forEach(ev => {
                        const typeClass = ev.eventtype === 'user' ? 'user' : '';
                        eventsHtml += `
                        <li class="event-item ${typeClass}">
                            <span class="calendar-circle calendar_event_${ev.eventtype || 'user'}"></span>
                            <span class="eventname">${ev.name || ev.title}</span>
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

            // Compléter la dernière ligne
            while (cellCount < 7) {
                row.innerHTML += '<td class="dayblank">&nbsp;</td>';
                cellCount++;
            }
            calendarBody.appendChild(row);
        }

        // ===================================
        // Navigation
        // ===================================
        prevMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        // ===================================
        // Initialisation
        // ===================================
        fetchEvents();

        // Le reste de ton code modal reste identique (toggleModal, formulaire, etc.)
        // Tu peux le garder tel quel.
    });
</script>
