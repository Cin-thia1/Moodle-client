@extends('layouts.app')

@section('title', 'Créer une section - ' . $course->fullname)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left"></i> Retour au cours
            </a>
            <h1 class="text-4xl font-bold text-gray-900">Créer une section</h1>
            <p class="text-gray-600 mt-1">Ajoutez une nouvelle section au cours {{ $course->fullname }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('sections.store', $course) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Section Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nom de la section <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Ex: Introduction aux concepts..."
                        value="{{ old('name') }}"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('courses.show', $course) }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium flex items-center gap-2">
                        <i class="fas fa-plus"></i> Créer la section
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- Modale d'ajout de ressource -->
        <div id="addResourceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
            <div class="bg-white p-8 rounded w-2/3 md:w-1/2 lg:w-1/3">
                <h2 class="text-xl mb-4">Ajouter une Ressource</h2>
                <form action="" method="POST">
                    @csrf
                    <!-- Champs du formulaire de ressource -->
                    <input type="text" name="resource_name" placeholder="Resource Name" class="border-2 p-2 mb-4 w-full" required>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Enregistrer la Ressource</button>
                </form>
                <x-button class="mt-4 text-red-500" id="closeResourceModal">Fermer</x-button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Récupérer les éléments de la modale et des boutons
    const addActivityBtn = document.getElementById('addActivityBtn');
    const addResourceBtn = document.getElementById('addResourceBtn');
    const addActivityModal = document.getElementById('addActivityModal');
    const addResourceModal = document.getElementById('addResourceModal');
    const closeActivityModal = document.getElementById('closeActivityModal');
    const closeResourceModal = document.getElementById('closeResourceModal');

    // Ouvrir la modale d'ajout d'activité
    addActivityBtn.addEventListener('click', () => {
        addActivityModal.classList.remove('hidden');
    });

    // Ouvrir la modale d'ajout de ressource
    addResourceBtn.addEventListener('click', () => {
        addResourceModal.classList.remove('hidden');
    });

    // Fermer la modale d'ajout d'activité
    closeActivityModal.addEventListener('click', () => {
        addActivityModal.classList.add('hidden');
    });

    // Fermer la modale d'ajout de ressource
    closeResourceModal.addEventListener('click', () => {
        addResourceModal.classList.add('hidden');
    });
</script>
@endsection
