@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center">
    <h1 class='text-2xl font-bold'>Ajouter une section au Cours <span class="text-primary text-3xl">{{ $course->fullname }}</span></h1>
    <hr class="w-full h-[2px] mt-2 mb-6 bg-black" />

    <div class='flex w-full justify-between'>
        <!-- Formulaire de création de section -->
        <form action="" method="POST" class="flex flex-col gap-6 w-full text-xl">
            @csrf

            <fieldset>
                <!-- Fullname Field -->
                <div class="flex items-center">
                    <label for="fullname" class="text-nowrap mr-4">Nom :</label>
                    <input type="text" name="fullname" class="py-1 rounded-md w-2/5 border-0 border-b-2 border-black" id="fullname" required>
                </div>
            </fieldset>
            <fieldset class="w-full border border-dotted border-primary p-3 rounded-md">
                <legend>SECTION CONTENT</legend>

                <div class="mt-10">
                    <!-- Bouton pour afficher la modale pour ajouter une activité -->
                    <x-button id="addActivityBtn">
                        Ajouter une Activité
                    </x-button>

                    <!-- Bouton pour afficher la modale pour ajouter une ressource -->
                    <x-button id="addResourceBtn">
                        Ajouter une Ressource
                    </x-button>
                </div>
            </fieldset>
        </form>

        <!-- Modale d'ajout d'activité -->
        <div id="addActivityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
            <div class="bg-white p-8 rounded w-2/3 md:w-1/2 lg:w-1/3">
                <h2 class="text-xl mb-4">Ajouter une Activité</h2>
                <form action="" method="POST">
                    @csrf
                    <!-- Champs du formulaire d'activité -->
                    <input type="text" name="activity_name" placeholder="Activity Name" class="border-2 p-2 mb-4 w-full" required>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Enregistrer l'Activité</button>
                </form>
                <x-button class="mt-4 text-red-500" id="closeActivityModal">Fermer</x-button>
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
