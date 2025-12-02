@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Créer un Nouveau Module</h1>

    <form action="{{ route('modules.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nom du Module</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="modname" class="block text-sm font-medium text-gray-700">Type de Module</label>
            <select name="modname" id="modname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required onchange="updateModplural()">
                <option value="resource">Ressource</option>
                <option value="assign">Devoir</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="file_path" class="block text-sm font-medium text-gray-700">Téléverser une Ressource</label>
            <input type="file" name="file_path" id="file_path" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <!-- Champ hidden pour downloadcontent -->
        <input type="hidden" name="downloadcontent" value="1">

        <!-- Champ hidden pour modplural -->
        <input type="hidden" name="modplural" id="modplural" value="Files">

        <input type="hidden" name="section_id" value="{{ $sectionId }}">

        <div class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Créer le Module
            </button>
        </div>
    </form>
</div>

<script>
    // Fonction pour mettre à jour la valeur de modplural en fonction du choix de modname
    function updateModplural() {
        const modname = document.getElementById('modname').value;
        const modplural = document.getElementById('modplural');

        if (modname === 'resource') {
            modplural.value = 'Files';
        } else if (modname === 'assign') {
            modplural.value = 'Assignments';
        }
    }
</script>
@endsection