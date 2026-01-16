<nav x-data="{ open: false, isSyncing: false }" class="bg-white/90 backdrop-blur-sm shadow-md sticky top-0 z-50 border-b border-gray-200/80">
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
                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">
                        {{ __('Cours') }}
                    </x-nav-link>
                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.index')">
                        {{ __('Évaluations') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-nav-link>

                    @role('ROLE_ADMIN')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endrole
                </nav>
            </div>

            <!-- Right side Actions & User Menu -->
            <div class="hidden sm:flex items-center gap-4">

                <!-- Sync Button -->
                <form id="syncForm" action="{{ route('synchronisation') }}" method="POST" class="flex items-center">
                    @csrf
                    <button
                        type="submit"
                        @click="isSyncing = true; setTimeout(() => isSyncing = false, 3000)"
                        :disabled="isSyncing"
                        class="relative p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300"
                        aria-label="Actualiser les données"
                    >
                        <i class="fas fa-sync-alt h-5 w-5" :class="{'animate-spin': isSyncing}"></i>
                    </button>
                </form>

                <!-- Icon Links -->
                <a href="/about" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition-colors duration-300" aria-label="À propos de nous">
                    <i class="fas fa-info-circle h-5 w-5"></i>
                </a>
                <a href="/contact" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition-colors duration-300" aria-label="Nous contacter">
                    <i class="fas fa-envelope h-5 w-5"></i>
                </a>

                <div class="w-px h-6 bg-gray-200 mx-2"></div>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 text-sm font-medium text-gray-600 hover:text-indigo-600 focus:outline-none transition-all duration-300">
                            <span class="hidden md:inline">{{ Auth::user()->name ?? 'Invité' }}</span>
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
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
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
                            <x-dropdown-link :href="route('register')" class="flex items-center gap-3">
                                <i class="fa-solid fa-user-plus w-5 h-5 text-gray-400"></i>
                                {{ __('S\'inscrire') }}
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
            <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.index')">{{ __('Évaluations') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Tableau de bord') }}</x-responsive-nav-link>
             @role('ROLE_ADMIN')
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('Admin') }}</x-responsive-nav-link>
             @endrole
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
                        <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
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
                    <x-responsive-nav-link :href="route('register')">{{ __('S\'inscrire') }}</x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>