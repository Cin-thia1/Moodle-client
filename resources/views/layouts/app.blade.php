<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Un titre de page dynamique pour un meilleur SEO et une meilleure expérience utilisateur --}}
    <title>@yield('title', 'Tableau de bord') | {{ config('app.name', 'LearnPlatform') }}</title>

    {{-- Placeholder pour votre favicon --}}
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> {{-- Pour les icônes --}}

    <!-- Scripts et Styles avec Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js pour l'interactivité -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Pour éviter le "flash" des éléments Alpine au chargement de la page --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800">

    {{-- =================================================== --}}
    {{-- == SYSTÈME DE NOTIFICATION MODERNE AVEC ALPINE.JS == --}}
    {{-- =================================================== --}}
    <div x-data="notifications()" class="fixed top-5 right-5 z-50 w-full max-w-sm space-y-3">
        <template x-for="notification in notifications" :key="notification.id">
            <div
                x-show="notification.show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 translate-x-full"
                x-transition:enter-end="transform opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="transform opacity-100 translate-x-0"
                x-transition:leave-end="transform opacity-0 translate-x-full"
                @click="remove(notification.id)"
                class="relative bg-white rounded-xl shadow-2xl p-4 border-l-4 cursor-pointer"
                :class="{
                    'border-green-500': notification.type === 'success',
                    'border-red-500': notification.type === 'error',
                    'border-amber-500': notification.type === 'alert'
                }"
            >
                <div class="flex items-start gap-3">
                    <div class="shrink-0 text-xl" :class="{
                        'text-green-500': notification.type === 'success',
                        'text-red-500': notification.type === 'error',
                        'text-amber-500': notification.type === 'alert'
                    }">
                        <i x-show="notification.type === 'success'" class="fas fa-check-circle"></i>
                        <i x-show="notification.type === 'error'" class="fas fa-times-circle"></i>
                        <i x-show="notification.type === 'alert'" class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700" x-text="notification.message"></p>
                </div>
            </div>
        </template>
    </div>

    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Contenu principal de la page -->
        <main class="py-8">
            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- =================================================== --}}
                {{-- == FIL D'ARIANE (BREADCRUMBS) AMÉLIORÉ         == --}}
                {{-- =================================================== --}}
                @php
                    $segments = array_filter(request()->segments());
                    $breadcrumbs = [['name' => 'Accueil', 'url' => url('/')]];
                    $path = '';
                    $translations = ['dashboard' => 'Tableau de bord', 'courses' => 'Cours', 'create' => 'Créer', 'edit' => 'Modifier', 'users' => 'Utilisateurs', 'profile' => 'Profil', 'assignments' => 'Évaluations', 'submissions' => 'Soumissions'];
                    foreach ($segments as $segment) {
                        if(is_numeric($segment)) continue; // Ignorer les ID
                        $path .= '/' . $segment;
                        $title = $translations[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
                        $breadcrumbs[] = ['name' => $title, 'url' => url($path)];
                    }
                @endphp

                @if(count($breadcrumbs) > 1)
                    <nav aria-label="Breadcrumb" class="mb-8">
                        <ol class="flex items-center space-x-2 text-sm">
                            @foreach ($breadcrumbs as $breadcrumb)
                                <li>
                                    <div class="flex items-center">
                                        @if (!$loop->first)
                                            <svg class="h-5 w-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                                        @endif
                                        <a href="{{ $breadcrumb['url'] }}" class="@if(!$loop->last) ml-2 text-gray-500 hover:text-indigo-600 @else ml-2 font-medium text-gray-800 pointer-events-none @endif transition-colors">
                                            {{ $breadcrumb['name'] }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                {{-- Slot pour un en-tête de page optionnel --}}
                @if (isset($header))
                    <header class="mb-8 border-b border-gray-200 pb-6">
                        {{ $header }}
                    </header>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
    // ===============================================
    // == LOGIQUE ALPINE.JS POUR LES NOTIFICATIONS  ==
    // ===============================================
    function notifications() {
        return {
            notifications: [],
            id: 0,
            add(notification) {
                this.notifications.push({
                    id: this.id++,
                    message: notification.message,
                    type: notification.type,
                    show: true
                });
                setTimeout(() => {
                    this.notifications[this.notifications.length - 1].show = false;
                }, 4000);
            },
            remove(id) {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index > -1) {
                    this.notifications[index].show = false;
                }
            }
        }
    }
    </script>

    {{-- Afficher les notifications de session Laravel au chargement de la page --}}
    @if(session('success'))
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('notifications').add({ type: 'success', message: "{{ session('success') }}" });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('notifications').add({ type: 'error', message: "{{ session('error') }}" });
            });
        </script>
    @endif
    @if(session('alert'))
         <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('notifications').add({ type: 'alert', message: "{{ session('alert') }}" });
            });
        </script>
    @endif

    {{-- Un stack pour les scripts spécifiques à chaque page --}}
    @stack('scripts')
</body>
</html>