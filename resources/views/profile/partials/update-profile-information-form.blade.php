<section x-data="{ photoPreview: null }" class="bg-white p-8 sm:p-12 rounded-2xl border border-gray-200/80 shadow-sm">

    {{-- En-tête de la section --}}
    <header class="mb-10 pb-8 border-b border-gray-200">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">
            {{ __('Profil') }}
        </h2>
        <p class="mt-3 text-base leading-relaxed text-gray-600 max-w-2xl">
            {{ __("Modifiez ici vos informations personnelles. Ces informations seront visibles par les autres utilisateurs sur la plateforme.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            {{-- Libellé de la section Photo --}}
            <div class="md:col-span-1">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Photo de profil</h3>
                <p class="mt-1 text-sm text-gray-600">Une photo de profil aide les autres à vous reconnaître.</p>
            </div>

            {{-- Interaction pour la photo --}}
            <div class="md:col-span-2 flex items-center gap-6">
                <div class="shrink-0">
                    <img x-show="!photoPreview" src="{{ $user->profile_picture_url }}" alt="Photo de profil actuelle" class="h-28 w-28 rounded-full object-cover">
                    <span x-show="photoPreview" class="block h-28 w-28 rounded-full bg-cover bg-no-repeat bg-center" :style="'background-image: url(\'' + photoPreview + '\');'"></span>
                </div>
                <button @click.prevent="$refs.photo.click()" type="button" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-600">
                    {{ __('Changer la photo') }}
                </button>
                <input type="file" name="profile_picture" id="photo" class="hidden" x-ref="photo" @change="
                    const file = $refs.photo.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => { photoPreview = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                ">
            </div>
             <div class="md:col-start-2 md:col-span-2">
                 <x-input-error :messages="$errors->get('profile_picture')" />
             </div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3 border-t border-gray-200 pt-8">
            {{-- Libellé de la section Informations personnelles --}}
            <div class="md:col-span-1">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Informations personnelles</h3>
                <p class="mt-1 text-sm text-gray-600">Vos nom et adresse e-mail.</p>
            </div>

            {{-- Champs Nom et Email --}}
            <div class="md:col-span-2 space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Nom complet')" />
                    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-lg border-gray-300 py-3 px-4 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Adresse e-mail')" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-lg border-gray-300 py-3 px-4 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
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
                <p x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-in-out" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 2000)" class="text-sm font-medium text-gray-700">{{ __('Enregistré.') }}</p>
            @endif
            <x-primary-button class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg shadow-sm hover:bg-indigo-700 hover:-translate-y-px transform transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Enregistrer') }}</x-primary-button>
        </div>
    </form>
</section>