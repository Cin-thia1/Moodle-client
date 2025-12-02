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
                            <select id="timeline-filter" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <select id="timeline-sort" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="dates">Dates</option>
                                <option value="courses">Cours</option>
                            </select>
                        </div>
                    </div>

                    <!-- État vide -->
                    <div class="flex flex-col gap-4 justify-center items-center border-t border-gray-200 mt-8 py-12 text-gray-400 text-center">
                        <i class="far fa-calendar-check text-6xl text-gray-300"></i>
                        <span class="text-lg font-medium">Aucune activité à venir</span>
                        <p class="text-sm">Les devoirs et dates limites apparaîtront ici.</p>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Calendrier -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Calendrier</h2>
                    <div class="flex items-center gap-4">
                        <select id="course-category" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">Tous les cours</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                            @endforeach
                        </select>
                        <button id="openModalBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            <i class="fas fa-plus"></i>
                            Nouvel événement
                        </button>
                    </div>
                </div>

                <!-- Calendrier -->
                <div class="select-none">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-100 rounded-t-lg">
                        <button id="prevMonth" class="p-2 rounded-full hover:bg-gray-200 transition-colors"><i class="fas fa-chevron-left text-gray-600"></i></button>
                        <h2 id="currentMonth" class="text-lg font-bold text-gray-800"></h2>
                        <button id="nextMonth" class="p-2 rounded-full hover:bg-gray-200 transition-colors"><i class="fas fa-chevron-right text-gray-600"></i></button>
                    </div>
                    <div class="grid grid-cols-7 text-center text-sm font-medium text-gray-500 border-l border-r border-gray-200">
                        <div class="py-2">Lun</div><div class="py-2">Mar</div><div class="py-2">Mer</div><div class="py-2">Jeu</div><div class="py-2">Ven</div><div class="py-2">Sam</div><div class="py-2">Dim</div>
                    </div>
                    <div class="grid grid-cols-7 h-[500px] text-sm" id="calendar">
                        <!-- Les jours du calendrier seront injectés ici par JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour créer un événement -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
        <div id="modalContent" class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all opacity-0 -translate-y-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Créer un nouvel événement</h2>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 transition-colors"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <form id="eventForm" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date & Heure</label>
                    <input type="datetime-local" id="date" name="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="utilisateur" selected>Utilisateur</option>
                        <option value="cours">Cours</option>
                        <option value="categorie">Catégorie</option>
                        <option value="site">Site</option>
                    </select>
                </div>
                <div id="courseField" style="display: none;">
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Cours</label>
                    <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($courses as $course) <option value="{{ $course->id }}">{{ $course->fullname }}</option> @endforeach
                    </select>
                </div>
                <div id="categoryField" style="display: none;">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($categories as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                    </select>
                </div>
                <div class="pt-4 flex justify-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        Enregistrer l'événement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===================================
    // == Éléments du DOM et État Initial ==
    // ===================================
    const calendarEl = document.getElementById('calendar');
    const currentMonthEl = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const eventModal = document.getElementById('eventModal');
    const modalContent = document.getElementById('modalContent');
    const eventForm = document.getElementById('eventForm');
    const typeSelect = document.getElementById('type');

    let state = {
        date: new Date(),
        events: []
    };

    // ===================================
    // == Fonctions Principales         ==
    // ===================================

    /**
     * Affiche une notification non-bloquante
     * @param {string} message - Le message à afficher
     * @param {string} type - 'success' (vert) ou 'error' (rouge)
     */
    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-times-circle';

        const notification = document.createElement('div');
        notification.className = `fixed top-5 right-5 ${bgColor} text-white px-5 py-3 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `<i class="fas ${icon} mr-2"></i> ${message}`;

        document.body.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 10);

        // Animation de sortie
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Récupère les événements depuis le serveur
     */
    async function fetchEvents() {
        try {
            const response = await fetch('/events');
            if (!response.ok) throw new Error('Network response was not ok');
            state.events = await response.json();
            renderCalendar();
        } catch (error) {
            console.error('Failed to fetch events:', error);
            showNotification("Erreur lors du chargement des événements.", 'error');
        }
    }

    /**
     * Génère et affiche le calendrier pour le mois en cours
     */
    function renderCalendar() {
        const year = state.date.getFullYear();
        const month = state.date.getMonth();

        const firstDayOfMonth = new Date(year, month, 1).getDay(); // 0=Sun, 1=Mon...
        const adjustedFirstDay = (firstDayOfMonth === 0) ? 6 : firstDayOfMonth - 1; // 0=Mon...
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        currentMonthEl.textContent = state.date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        calendarEl.innerHTML = '';

        // Jours vides au début
        for (let i = 0; i < adjustedFirstDay; i++) {
            calendarEl.innerHTML += `<div class="border-t border-l border-gray-200"></div>`;
        }

        // Jours du mois
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const dayEvents = state.events.filter(e => {
                const eventDate = new Date(e.date ?? (e.timestart * 1000));
                return eventDate.getDate() === day && eventDate.getMonth() === month && eventDate.getFullYear() === year;
            });

            let dayHtml = `
                <div class="border-t border-l border-gray-200 p-2 h-full flex flex-col ${isToday ? 'bg-indigo-50' : ''}">
                    <div class="font-semibold ${isToday ? 'text-indigo-600' : 'text-gray-700'}">${day}</div>
                    <ul class="mt-1 space-y-1 overflow-y-auto text-xs flex-grow">
                        ${dayEvents.map(e => `<li class="bg-indigo-100 text-indigo-800 p-1 rounded truncate">${e.title ?? e.name}</li>`).join('')}
                    </ul>
                </div>
            `;
            calendarEl.innerHTML += dayHtml;
        }
    }

    /**
     * Gère l'affichage du modal
     */
    function toggleModal(show) {
        if (show) {
            eventModal.classList.remove('hidden');
            setTimeout(() => modalContent.classList.remove('opacity-0', '-translate-y-4'), 10);
        } else {
            modalContent.classList.add('opacity-0', '-translate-y-4');
            setTimeout(() => {
                eventModal.classList.add('hidden');
                eventForm.reset();
                toggleEventTypeFields();
            }, 200);
        }
    }

    /**
     * Gère l'affichage des champs conditionnels dans le formulaire du modal
     */
    function toggleEventTypeFields() {
        const type = typeSelect.value;
        document.getElementById("courseField").style.display = (type === "cours") ? "block" : "none";
        document.getElementById("categoryField").style.display = (type === "categorie") ? "block" : "none";
    }

    // ===================================
    // == Écouteurs d'événements        ==
    // ===================================
    prevMonthBtn.addEventListener('click', () => {
        state.date.setMonth(state.date.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        state.date.setMonth(state.date.getMonth() + 1);
        renderCalendar();
    });

    openModalBtn.addEventListener('click', () => toggleModal(true));
    closeModalBtn.addEventListener('click', () => toggleModal(false));
    eventModal.addEventListener('click', e => e.target === eventModal && toggleModal(false));
    typeSelect.addEventListener('change', toggleEventTypeFields);

    eventForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const response = await fetch('/events', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': formData.get('_token') }
            });
            if (!response.ok) throw new Error('Server responded with an error');
            await response.json();
            showNotification('Événement créé avec succès !');
            toggleModal(false);
            fetchEvents(); // Recharger les événements
        } catch (error) {
            console.error('Failed to submit event:', error);
            showNotification("Erreur lors de la création de l'événement.", 'error');
        }
    });

    // ===================================
    // == Initialisation                ==
    // ===================================
    fetchEvents();
    @if(session('success'))
        showNotification("{{ session('success') }}");
    @endif
});
</script>
@endsection