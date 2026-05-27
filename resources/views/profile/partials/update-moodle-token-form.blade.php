<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Connexion Moodle') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Saisissez le token API que l\'administrateur Moodle vous a transmis.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-moodle-token') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="moodle_token" :value="__('Token API Moodle')" />
            <div class="relative">
                <x-text-input id="moodle_token" name="moodle_token" type="password" class="mt-1 block w-full pr-10" :value="old('moodle_token', $user->moodle_token)" required autocomplete="off" />
                <button type="button" onclick="toggleTokenVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600" id="token-eye-icon"></i>
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('moodle_token')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>

            @if (session('status') === 'token-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Sauvegardé.') }}</p>
            @endif
            
            @if($user->moodle_token)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fa-solid fa-check-circle"></i> Token configuré
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fa-solid fa-exclamation-circle"></i> Token manquant
                </span>
            @endif
        </div>
    </form>

    <script>
        function toggleTokenVisibility() {
            const tokenInput = document.getElementById('moodle_token');
            const eyeIcon = document.getElementById('token-eye-icon');
            
            if (tokenInput.type === 'password') {
                tokenInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                tokenInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</section>
