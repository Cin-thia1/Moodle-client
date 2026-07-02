<x-guest-layout>
    <x-slot name="rightPanel">
        <!-- Carousel pur HTML/CSS/JS - sans Alpine pour éviter les conflits -->
        <div id="login-carousel" class="h-full w-full relative overflow-hidden">

            <!-- Slides -->
            @php
                $carouselSlides = [
                    [
                        'image'  => asset('images/login-slide-1.avif'),
                        'quote'  => "Grâce à cette plateforme, j'ai pu suivre mes cours même sans connexion permanente. Un vrai atout pour nous, étudiants camerounais !",
                        'author' => 'Aminatou Bello',
                        'role'   => 'Étudiante en Informatique, Université de Yaoundé I',
                    ],
                    [
                        'image'  => asset('images/login-slide-2.avif'),
                        'quote'  => "Apprendre ensemble, partager le savoir — cette plateforme nous rapproche et renforce la solidarité entre étudiants africains.",
                        'author' => 'Jean-Baptiste Nkoa',
                        'role'   => 'Enseignant, Institut Universitaire de Technologie',
                    ],
                    [
                        'image'  => asset('images/login-slide-3.jpeg'),
                        'quote'  => "L'éducation est la clé du développement de l'Afrique. Avec Moodle Client, le savoir n'a plus de frontières ni de contraintes réseau.",
                        'author' => 'Dr. Marie-Claire Essomba',
                        'role'   => 'Directrice Pédagogique, Université de Douala',
                    ],
                ];
            @endphp

            @foreach($carouselSlides as $i => $slide)
            <div class="carousel-slide absolute inset-0 w-full h-full transition-opacity duration-1000 {{ $i === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                 data-slide="{{ $i }}">
                <!-- Image de fond -->
                <img src="{{ $slide['image'] }}"
                     alt="Slide {{ $i + 1 }}"
                     class="absolute inset-0 w-full h-full object-cover" />
                <!-- Overlay sombre -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
                <!-- Texte -->
                <div class="absolute bottom-12 left-10 right-10 text-white z-10 bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 shadow-2xl">
                    <p class="text-xl md:text-2xl font-medium leading-relaxed mb-6 italic">
                        « {{ $slide['quote'] }} »
                    </p>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-lg">{{ $slide['author'] }}</h4>
                            <p class="text-indigo-200 text-sm">{{ $slide['role'] }}</p>
                        </div>
                        <!-- Boutons navigation -->
                        <div class="flex gap-3">
                            <button onclick="carouselPrev()" class="h-10 w-10 rounded-full border border-white/40 flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button onclick="carouselNext()" class="h-10 w-10 rounded-full border border-white/40 flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Indicateurs -->
            <div class="absolute top-8 right-10 flex gap-2 z-20">
                @foreach($carouselSlides as $i => $slide)
                <button onclick="carouselGoTo({{ $i }})"
                        class="carousel-dot h-2 rounded-full transition-all duration-300 hover:bg-white {{ $i === 0 ? 'w-8 bg-white' : 'w-2 bg-white/50' }}"
                        data-dot="{{ $i }}"></button>
                @endforeach
            </div>
        </div>

        <script>
            (function() {
                var current = 0;
                var slides = document.querySelectorAll('.carousel-slide');
                var dots   = document.querySelectorAll('.carousel-dot');
                var total  = slides.length;
                var timer;

                function goTo(n) {
                    slides[current].classList.remove('opacity-100');
                    slides[current].classList.add('opacity-0', 'pointer-events-none');
                    dots[current].classList.remove('w-8', 'bg-white');
                    dots[current].classList.add('w-2', 'bg-white/50');

                    current = (n + total) % total;

                    slides[current].classList.remove('opacity-0', 'pointer-events-none');
                    slides[current].classList.add('opacity-100');
                    dots[current].classList.remove('w-2', 'bg-white/50');
                    dots[current].classList.add('w-8', 'bg-white');
                }

                window.carouselNext = function() { clearInterval(timer); goTo(current + 1); startAuto(); };
                window.carouselPrev = function() { clearInterval(timer); goTo(current - 1); startAuto(); };
                window.carouselGoTo = function(n) { clearInterval(timer); goTo(n); startAuto(); };

                function startAuto() { timer = setInterval(function(){ goTo(current + 1); }, 6000); }
                startAuto();
            })();
        </script>
    </x-slot>

    <!-- Main Form Content -->
    <div class="w-full max-w-sm mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Bienvenue</h2>
        <p class="text-gray-500 mb-8">Heureux de vous revoir ! Veuillez entrer vos identifiants.</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email / Username -->
            <div>
                <label for="login" class="block text-sm font-semibold text-gray-700 mb-1">Email ou nom d'utilisateur</label>
                <input id="login" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                       type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="Entrez votre email ou identifiant" />
                <x-input-error :messages="$errors->get('login')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                <input id="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                       type="password" name="password" required autocomplete="current-password" placeholder="Entrez votre mot de passe" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Links -->
            <div class="flex items-center justify-between mt-2">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-blue-500 hover:text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                {{ __('Se connecter') }}
            </button>
        </form>

        @if (Route::has('register'))
        <!-- Sign Up Link -->
        <p class="mt-8 text-center text-sm text-gray-600">
            Vous n'avez pas de compte ? 
            <a href="{{ route('register') }}" class="font-bold text-blue-500 hover:text-blue-600 hover:underline">Inscrivez-vous</a>
        </p>
        @endif
    </div>
</x-guest-layout>
