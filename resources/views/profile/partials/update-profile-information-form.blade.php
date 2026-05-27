<section x-data="{ photoPreview: null }" class="bg-white rounded-2xl">

    {{-- En-tête de la section --}}
    <header class="mb-10 pb-8 border-b border-gray-200">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">
            {{ __('Profil') }}
        </h2>
        <p class="mt-3 text-base leading-relaxed text-gray-600">
            {{ __("Modifiez ici vos informations personnelles. Ces informations seront visibles par les autres utilisateurs sur la plateforme.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-10" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- ================================================ --}}
        {{-- Section Photo de profil (pleine largeur)          --}}
        {{-- ================================================ --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-8 pb-10 border-b border-gray-200">
            {{-- Photo actuelle + aperçu --}}
            <div class="shrink-0">
                <img
                    x-show="!photoPreview"
                    src="{{ $user->profile_picture_url }}"
                    alt="Photo de profil actuelle"
                    class="h-32 w-32 rounded-full object-cover ring-4 ring-indigo-100 shadow"
                >
                <span
                    x-show="photoPreview"
                    x-cloak
                    class="block h-32 w-32 rounded-full bg-cover bg-no-repeat bg-center ring-4 ring-indigo-300 shadow"
                    :style="'background-image: url(\'' + photoPreview + '\');'"
                ></span>
            </div>

            {{-- Infos + bouton --}}
            <div class="flex-1">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Photo de profil</h3>
                <p class="mt-1 text-sm text-gray-600">Une photo de profil aide les autres à vous reconnaître. JPG, PNG ou GIF — max 2 Mo.</p>

                <button
                    @click.prevent="$refs.photo.click()"
                    type="button"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-100 transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                >
                    <i class="fas fa-camera"></i>
                    {{ __('Changer la photo') }}
                </button>

                <input
                    type="file"
                    name="profile_picture"
                    id="photo"
                    accept="image/*"
                    class="hidden"
                    x-ref="photo"
                    @change="
                        const file = $refs.photo.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    "
                >

                <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
            </div>
        </div>

        {{-- ================================================ --}}
        {{-- Section Informations personnelles                 --}}
        {{-- ================================================ --}}
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            {{-- Libellé --}}
            <div class="md:col-span-1">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Informations personnelles</h3>
                <p class="mt-1 text-sm text-gray-600">Votre nom complet et adresse e-mail.</p>
            </div>

            {{-- Champs --}}
            <div class="md:col-span-2 space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Nom complet')" />
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-2 block w-full rounded-lg border-gray-300 py-3 px-4 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring-indigo-500"
                        :value="old('name', $user->name)"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Adresse e-mail')" />
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-2 block w-full rounded-lg border-gray-300 py-3 px-4 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring-indigo-500"
                        :value="old('email', $user->email)"
                        required
                        autocomplete="username"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 text-sm text-gray-600">
                            {{ __('Votre adresse email n\'est pas vérifiée.') }}
                            <button form="send-verification" class="underline text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Renvoyer l\'email de vérification.') }}
                            </button>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('Un nouveau lien de vérification a été envoyé.') }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions de sauvegarde --}}
        <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-8">
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-in-out"
                    x-transition:leave="transition ease-in-out duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-green-600"
                >
                    <i class="fas fa-check-circle mr-1"></i>
                    {{ __('Profil mis à jour avec succès !') }}
                </p>
            @endif

            <x-primary-button class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg shadow-sm hover:bg-indigo-700 hover:-translate-y-px transform transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Enregistrer') }}
            </x-primary-button>
        </div>
    </form>

</section>