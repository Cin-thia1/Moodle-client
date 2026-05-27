<nav x-data="{ open: false, isSyncing: false, moodleOnline: null, wasOffline: false, checkConnection() { fetch('{{ route('sync.ping') }}', {headers: {'Accept': 'application/json'}}).then(r => r.json()).then(d => { if (d.loggedOut) { window.location.href = '/login'; return; } if (this.wasOffline && d.online && d.hasPending && typeof triggerAutoSync === 'function') { triggerAutoSync(); } this.wasOffline = !d.online; this.moodleOnline = d.online; }).catch(e => { console.error('Ping error:', e); this.wasOffline = true; this.moodleOnline = false; }); } }" x-init="checkConnection(); setInterval(() => checkConnection(), 15000)" class="bg-white/90 backdrop-blur-sm shadow-md sticky top-0 z-50 border-b border-gray-200/80">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <!-- Logo & Main Navigation -->
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <x-application-logo class="block h-10 w-auto text-indigo-600" />
                        <span class="font-bold text-xl text-gray-800 hidden lg:block">LearnPlatform</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden space-x-2 md:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Accueil') }}
                    </x-nav-link>
                    <x-nav-link :href="route('courses.index')" :active="request()->is('courses*')">
                        {{ __('Cours') }}
                    </x-nav-link>
                    <x-nav-link
    :href="route('assignments.index')"
    :active="
        request()->routeIs('assignments.*')
        || request()->routeIs('courses.gradebook')
        || request()->routeIs('gradebook.save')
    "
>
    {{ __('Évaluations') }}
</x-nav-link>

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-nav-link>

                    @hasanyrole('ROLE_ADMIN|ROLE_MANAGER')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endhasanyrole
                </nav>
            </div>

            <!-- Right side Actions & User Menu -->
            <div class="hidden sm:flex items-center gap-4">

                <!-- Connection Status Indicator -->
                <div class="flex items-center justify-center mr-2" :title="moodleOnline === true ? 'Connecté à Moodle' : (moodleOnline === false ? 'Moodle Hors-ligne' : 'Vérification...')">
                    <span class="relative flex h-3 w-3">
                      <span x-show="moodleOnline === true" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span :class="moodleOnline === true ? 'bg-green-500' : (moodleOnline === false ? 'bg-red-500' : 'bg-gray-400')" class="relative inline-flex rounded-full h-3 w-3"></span>
                    </span>
                </div>

                <!-- Sync Button -->
                <a href="{{ route('sync.status') }}" class="flex items-center" title="Voir le statut de synchronisation">
                    <button
                        type="button"
                        class="relative p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300"
                        aria-label="Synchronisation"
                    >
                        <i class="fas fa-sync-alt h-5 w-5"></i>
                    </button>
                </a>

                <!-- Icon Links -->
                <a href="/about" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition-colors duration-300" aria-label="À propos de nous" title="À propos de nous">
                    <i class="fas fa-info-circle h-5 w-5"></i>
                </a>
                <a href="/contact" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition-colors duration-300" aria-label="Nous contacter" title="Nous contacter">
                    <i class="fas fa-envelope h-5 w-5"></i>
                </a>

                <div class="w-px h-6 bg-gray-200 mx-2"></div>

                <!-- Notifications Dropdown -->
                <div x-data="bellNotifications()" class="relative">
                    <button @click="open = !open" class="relative p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300" aria-label="Notifications" title="Notifications">
                        <i class="fas fa-bell h-5 w-5"></i>
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount" 
                              class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white shadow-sm animate-pulse"
                              x-cloak>
                        </span>
                    </button>

                    <!-- Dropdown panel -->
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                         class="absolute right-0 mt-3 w-96 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-100 shadow-2xl z-50 overflow-hidden"
                         style="display: none;"
                         x-cloak>
                        
                        <!-- Header -->
                        <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50/50 to-white">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-800 text-sm">Notifications</span>
                                <template x-if="courseId">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100/50">
                                        Cours actuel
                                    </span>
                                </template>
                            </div>
                            <button @click="markAllAsRead()" 
                                    x-show="unreadCount > 0"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                Tout lire
                            </button>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-[350px] overflow-y-auto divide-y divide-gray-50 scrollbar-thin">
                            
                            <!-- Loading state -->
                            <div x-show="loading && notifications.length === 0" class="py-12 flex flex-col items-center justify-center text-gray-400 gap-2">
                                <i class="fas fa-spinner fa-spin text-xl text-indigo-500"></i>
                                <span class="text-xs font-medium">Chargement...</span>
                            </div>

                            <!-- Empty state -->
                            <div x-show="!loading && notifications.length === 0" class="py-12 flex flex-col items-center justify-center text-gray-400 gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-bell-slash text-base"></i>
                                </div>
                                <span class="text-xs font-medium">Aucune notification</span>
                                <p class="text-[11px] text-gray-400 px-6 text-center" x-text="courseId ? 'Pas de nouvelles activités pour ce cours' : 'Vous êtes à jour !'"></p>
                            </div>

                            <!-- Notification item -->
                            <template x-for="n in notifications" :key="n.key">
                                <div @click="markAsRead(n)" 
                                     class="p-4 hover:bg-gray-50/80 cursor-pointer transition-colors duration-200 flex gap-3 relative"
                                     :class="!n.read ? 'bg-indigo-50/10' : ''">
                                    
                                    <!-- Unread indicator dot -->
                                    <div x-show="!n.read" class="absolute top-4 right-4 w-2 h-2 rounded-full bg-indigo-600"></div>

                                    <!-- Icon wrapper -->
                                    <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-sm"
                                         :class="{
                                             'bg-green-50 text-green-600': n.type === 'welcome',
                                             'bg-blue-50 text-blue-600': n.type === 'assignment',
                                             'bg-purple-50 text-purple-600': n.type === 'quiz',
                                             'bg-amber-50 text-amber-600': n.type === 'document',
                                             'bg-rose-50 text-rose-600': n.type === 'announcement'
                                         }">
                                        <i :class="{
                                            'fas fa-graduation-cap': n.type === 'welcome',
                                            'fas fa-file-signature': n.type === 'assignment',
                                            'fas fa-puzzle-piece': n.type === 'quiz',
                                            'fas fa-file-alt': n.type === 'document',
                                            'fas fa-bullhorn': n.type === 'announcement'
                                        }"></i>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0 pr-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[9px] font-bold tracking-wide uppercase text-gray-400 truncate max-w-[120px]" x-text="n.course_name"></span>
                                            <span class="text-[9px] text-gray-400 shrink-0 ml-2" x-text="formatDate(n.date)"></span>
                                        </div>
                                        <h4 class="text-xs font-semibold text-gray-800 truncate mb-0.5" x-text="n.title"></h4>
                                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed" x-text="n.message"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-2 bg-gray-50/50 border-t border-gray-100/80 text-center">
                            <span class="text-[9px] text-gray-400 font-medium" x-text="courseId ? 'Notifications filtrées pour ce cours' : 'Dernières notifications'"></span>
                        </div>
                    </div>
                </div>

                <div class="w-px h-6 bg-gray-200 mx-2"></div>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 text-sm font-medium text-gray-600 hover:text-indigo-600 focus:outline-none transition-all duration-300">
                            @php
                                $roleName = Auth::check() ? Auth::user()->getRoleNames()->first() : null;

                                $roleLabel = match ($roleName) {
                                    'ROLE_TEACHER' => 'Enseignant',
                                    'ROLE_STUDENT' => 'Étudiant',
                                    'ROLE_ADMIN'   => 'Admin',
                                    'ROLE_MANAGER' => 'Manager',
                                    default        => 'Utilisateur',
                                };

                                $roleClass = match ($roleName) {
                                    'ROLE_TEACHER' => 'bg-indigo-100 text-indigo-700',
                                    'ROLE_STUDENT' => 'bg-green-100 text-green-700',
                                    'ROLE_MANAGER' => 'bg-purple-100 text-purple-700',
                                    default        => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="hidden md:inline flex items-center gap-2">
                                <span>{{ Auth::user()->name ?? 'Invité' }}</span>

                                @auth
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                @endauth
                            </span>

                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden">
                                @if(Auth::user() && Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="h-full w-full object-cover">
                                @else
                                    <span class="font-bold text-indigo-600">{{ Auth::user() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'G' }}</span>
                                @endif
                            </div>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <div class="px-4 py-2 border-b border-gray-100">
                                @php
                                    $roleName = Auth::user()->getRoleNames()->first();

                                    $roleLabel = match ($roleName) {
                                        'ROLE_TEACHER' => 'Enseignant',
                                        'ROLE_STUDENT' => 'Étudiant',
                                        'ROLE_ADMIN'   => 'Admin',
                                        'ROLE_MANAGER' => 'Manager',
                                        default        => 'Utilisateur',
                                    };

                                    $roleClass = match ($roleName) {
                                        'ROLE_TEACHER' => 'bg-indigo-100 text-indigo-700',
                                        'ROLE_STUDENT' => 'bg-green-100 text-green-700',
                                        'ROLE_MANAGER' => 'bg-purple-100 text-purple-700',
                                        default        => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-3">
                                <i class="fa-solid fa-user-circle w-5 h-5 text-gray-400"></i>
                                {{ __('Mon Profil') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-3 text-red-600 hover:!bg-red-50">
                                    <i class="fa-solid fa-sign-out-alt w-5 h-5 text-red-400"></i>
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="route('login')" class="flex items-center gap-3">
                                <i class="fa-solid fa-sign-in-alt w-5 h-5 text-gray-400"></i>
                                {{ __('Se connecter') }}
                            </x-dropdown-link>

                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200" x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Accueil') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">{{ __('Cours') }}</x-responsive-nav-link>
            <x-responsive-nav-link
    :href="route('assignments.index')"
    :active="
        request()->routeIs('assignments.*')
        || request()->routeIs('courses.gradebook')
        || request()->routeIs('gradebook.save')
    "
>
    {{ __('Évaluations') }}
</x-responsive-nav-link>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Tableau de bord') }}</x-responsive-nav-link>
             @hasanyrole('ROLE_ADMIN|ROLE_MANAGER')
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('Admin') }}</x-responsive-nav-link>
             @endhasanyrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="flex items-center px-4 mb-3">
                    <div class="shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden">
                         @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="h-full w-full object-cover">
                        @else
                            <span class="font-bold text-indigo-600">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="ms-3">
                        @php
                            $roleName = Auth::user()->getRoleNames()->first();

                            $roleLabel = match ($roleName) {
                                'ROLE_TEACHER' => 'Enseignant',
                                'ROLE_STUDENT' => 'Étudiant',
                                'ROLE_ADMIN'   => 'Admin',
                                'ROLE_MANAGER' => 'Manager',
                                default        => 'Utilisateur',
                            };

                            $roleClass = match ($roleName) {
                                'ROLE_TEACHER' => 'bg-indigo-100 text-indigo-700',
                                'ROLE_STUDENT' => 'bg-green-100 text-green-700',
                                'ROLE_MANAGER' => 'bg-purple-100 text-purple-700',
                                default        => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <div class="flex items-center gap-2">
                            <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $roleClass }}">
                                {{ $roleLabel }}
                            </span>
                        </div>

                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">{{ __('Mon Profil') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Déconnexion') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                 <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">{{ __('Se connecter') }}</x-responsive-nav-link>

                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
    function bellNotifications() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,
            loading: false,
            courseId: window.currentCourseId || null,
            
            init() {
                this.fetchNotifications();
                
                // Refresh notifications every 60 seconds
                setInterval(() => this.fetchNotifications(), 60000);
                
                this.$watch('open', value => {
                    if (value) {
                        this.fetchNotifications();
                    }
                });
            },
            
            fetchNotifications() {
                this.loading = true;
                let url = '/notifications';
                if (this.courseId) {
                    url += '?course_id=' + this.courseId;
                }
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Error fetching notifications:', err);
                    this.loading = false;
                });
            },
            
            markAsRead(notification) {
                if (notification.read) {
                    window.location.href = notification.url;
                    return;
                }
                
                fetch('/notifications/read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key: notification.key })
                })
                .then(res => {
                    notification.read = true;
                    if (this.unreadCount > 0) {
                        this.unreadCount--;
                    }
                    window.location.href = notification.url;
                })
                .catch(err => {
                    console.error('Error marking notification as read:', err);
                    window.location.href = notification.url;
                });
            },
            
            markAllAsRead() {
                const unreadKeys = this.notifications.filter(n => !n.read).map(n => n.key);
                if (unreadKeys.length === 0) return;
                
                fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ keys: unreadKeys })
                })
                .then(res => {
                    this.notifications.forEach(n => n.read = true);
                    this.unreadCount = 0;
                })
                .catch(err => {
                    console.error('Error marking all notifications as read:', err);
                });
            },
            
            formatDate(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                if (diffMins < 60) {
                    return diffMins <= 1 ? "À l'instant" : `Il y a ${diffMins} min`;
                } else if (diffHours < 24) {
                    return `Il y a ${diffHours} h`;
                } else if (diffDays < 7) {
                    return `Il y a ${diffDays} j`;
                } else {
                    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
                }
            }
        }
    }
</script>
