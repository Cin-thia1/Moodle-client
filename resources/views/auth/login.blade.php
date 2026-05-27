<x-guest-layout>
    <x-slot name="rightPanel">
        <!-- Carousel inside Alpine Component -->
        <div x-data="{
                activeSlide: 1,
                slides: [
                    {
                        image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                        quote: 'Moodle Client excels with its user-friendly interface, powerful features, and seamless integration capabilities.',
                        author: 'Christina Martin',
                        role: 'CTO, LearnPlatform'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                        quote: 'The synchronization is flawless. It completely transformed how we manage our courses offline.',
                        author: 'David Chen',
                        role: 'Lead Educator'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                        quote: 'A beautiful and modern approach to learning management systems.',
                        author: 'Sarah Jenkins',
                        role: 'Student'
                    }
                ],
                next() { this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1 },
                prev() { this.activeSlide = this.activeSlide === 1 ? this.slides.length : this.activeSlide - 1 }
            }" 
            x-init="setInterval(() => next(), 6000)"
            class="h-full w-full relative group">
            
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index + 1"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 transform scale-105"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-1000 absolute inset-0"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 h-full w-full">
                    
                    <!-- Background Image -->
                    <img :src="slide.image" alt="Background" class="absolute inset-0 w-full h-full object-cover opacity-90" />
                    <!-- Dark Gradient Overlay for text readability (neutral instead of blue) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    
                    <!-- Text Content at bottom -->
                    <div class="absolute bottom-12 left-10 right-10 text-white z-10 bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 shadow-2xl">
                        <p class="text-xl md:text-2xl font-medium leading-relaxed mb-6">"<span x-text="slide.quote"></span>"</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-lg" x-text="slide.author"></h4>
                                <p class="text-indigo-200 text-sm" x-text="slide.role"></p>
                            </div>
                            
                            <!-- Controls -->
                            <div class="flex gap-3">
                                <button @click="prev()" class="h-10 w-10 rounded-full border border-white/40 flex items-center justify-center hover:bg-white/20 transition-colors">
                                    <i class="fas fa-arrow-left text-sm"></i>
                                </button>
                                <button @click="next()" class="h-10 w-10 rounded-full border border-white/40 flex items-center justify-center hover:bg-white/20 transition-colors">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Indicators -->
            <div class="absolute top-8 right-10 flex gap-2 z-10">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index + 1" 
                            :class="{'w-8 bg-white': activeSlide === index + 1, 'w-2 bg-white/50': activeSlide !== index + 1}"
                            class="h-2 rounded-full transition-all duration-300 hover:bg-white/80"></button>
                </template>
            </div>
        </div>
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
