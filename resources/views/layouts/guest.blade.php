<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 bg-gray-50">
            @if (isset($rightPanel))
                <div class="flex w-full max-w-6xl bg-white shadow-2xl rounded-2xl overflow-hidden min-h-[600px]">
                    <!-- Left side: Form -->
                    <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative">
                        <div class="mb-10 flex items-center gap-4">
                            <a href="/">
                                <x-application-logo class="w-24 sm:w-32 h-auto fill-current text-indigo-600" />
                            </a>
                            <span class="font-bold text-2xl text-gray-900">LearnPlatform</span>
                        </div>
                        {{ $slot }}
                    </div>
                    <!-- Right side: Image/Carousel -->
                    <div class="hidden lg:block w-1/2 relative bg-indigo-900 overflow-hidden">
                        {{ $rightPanel }}
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center">
                    <div>
                        <a href="/">
                            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                        </a>
                    </div>
                    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                        {{ $slot }}
                    </div>
                </div>
            @endif
        </div>
        {{-- Toast Notification --}}
        <div id="sync-toast" class="fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-3 rounded shadow-lg hidden flex items-center gap-3 transition-opacity duration-300 z-50">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span id="sync-toast-text">Synchronisation avec Moodle...</span>
        </div>

        {{-- Script de Synchronisation Automatique --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('sync-toast');
                const toastText = document.getElementById('sync-toast-text');
                const spinner = toast.querySelector('svg');

                function triggerAutoSync() {
                    // Afficher le toast
                    toast.classList.remove('hidden');
                    toast.classList.remove('bg-green-600', 'bg-red-600');
                    toast.classList.add('bg-blue-600');
                    spinner.style.display = 'block';
                    toastText.innerText = 'Synchronisation initiale en cours...';

                    fetch('{{ route('sync.auto') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
                        return response.json();
                    })
                    .then(data => {
                        spinner.style.display = 'none';
                        if (data.status === 'success') {
                            toast.classList.replace('bg-blue-600', 'bg-green-600');
                            let created = data.summary?.pull?.created ?? 0;
                            let updated = data.summary?.pull?.updated ?? 0;
                            let errors = data.summary?.pull?.errors ?? 0;
                            
                            if (created > 0 || updated > 0) {
                                toastText.innerText = 'Synchronisation terminée : ' + created + ' créés, ' + updated + ' mis à jour.';
                            } else if (errors > 0) {
                                toastText.innerText = 'Synchronisation terminée avec ' + errors + ' erreur(s).';
                                toast.classList.replace('bg-green-600', 'bg-amber-500');
                            } else {
                                toastText.innerText = 'Synchronisation terminée : tout est à jour.';
                            }
                        } else if (data.status === 'already_running') {
                            toast.classList.replace('bg-blue-600', 'bg-green-600');
                            toastText.innerText = 'Système déjà à jour.';
                        } else {
                            toast.classList.replace('bg-blue-600', 'bg-red-600');
                            toastText.innerText = 'Erreur lors de la synchronisation.';
                        }
                        setTimeout(() => toast.classList.add('hidden'), 5000);
                    })
                    .catch(error => {
                        spinner.style.display = 'none';
                        toast.classList.replace('bg-blue-600', 'bg-red-600');
                        toastText.innerText = 'Erreur de connexion Moodle.';
                        console.error('Auto-Sync Error:', error);
                        setTimeout(() => toast.classList.add('hidden'), 5000);
                    });
                }

                // ❌ Auto-sync désactivée — synchronisation manuelle uniquement via /sync
                // setTimeout(triggerAutoSync, 2000);

                // ❌ Sync périodique désactivée
                // setInterval(triggerAutoSync, 180000);
            });
        </script>
    </body>
</html>
