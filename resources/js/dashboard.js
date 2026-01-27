
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
    function renderEventBadge(event) {
  const el = document.createElement('div');
  el.className = 'event-badge';
  el.textContent = event.name;

  // ✅ appliquer la couleur moodle
  if (event.color) {
    el.style.borderLeft = `4px solid ${event.color}`;
    el.style.backgroundColor = hexToRgba(event.color, 0.10);
    el.style.color = event.color;
  }

  return el;
}

// petit helper pour un fond léger
function hexToRgba(hex, alpha) {
  const h = hex.replace('#', '');
  const bigint = parseInt(h, 16);
  const r = (bigint >> 16) & 255;
  const g = (bigint >> 8) & 255;
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

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
    const safeData = JSON.stringify(ev).replace(/'/g, "&#39;");
    const color = ev.color || null;

    // ✅ Styles dynamiques basés sur la couleur Moodle
    const liStyle = color
        ? `border-left: 4px solid ${color}; background: ${hexToRgba(color, 0.10)};`
        : '';

    const circleStyle = color
        ? `background: ${color};`
        : '';

    eventsHtml += `
        <li class="event-item cursor-pointer transition-colors p-1 rounded"
            style="${liStyle}"
            data-event-id="${ev.id}"
            data-event-source="${ev.source || 'local'}"
            data-event-data='${safeData}'>
            <span class="calendar-circle" style="${circleStyle}"></span>
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
        
        const repeats = eventData.repeats ?? 0;
        repeatEventCheckbox.checked = repeats > 0;
        document.getElementById('repeat_count').value = (repeats > 0) ? repeats : 1;

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
    const source = sourceInput.value;  // Récupère la valeur du hidden input source

    let url = '/events';
    if (id) {
        url = `/events/${id}`;
        formData.append('_method', 'PUT');
    }
    // Ajoute toujours le paramètre source (même pour création, ça ne gêne pas)
    formData.append('source', source);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        let data = {};
        try { data = await response.json(); } catch(e) {}

        if (!response.ok) {
            let errorMsg = 'Erreur lors de l\'enregistrement';
    if (response.status === 422 && data.errors) {
        // Affiche tous les messages d'erreur
        errorMsg = Object.values(data.errors).flat().join('<br>');
        // Ou juste le premier
        // errorMsg = Object.values(data.errors)[0][0];
    } else if (data.message || data.error) {
        errorMsg = data.message || data.error;
    }
    // Utilise une notification plus visible
    alert(errorMsg); // Fallback si showNotification bug
    showNotification(errorMsg, 'error');
    return;
            return;
        }

        showNotification(data.message || 'Événement enregistré avec succès !');
        toggleModal(false);
        fetchEvents();  // Rafraîchit le calendrier
    } catch (error) {
        console.error(error);
        showNotification('Erreur réseau', 'error');
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