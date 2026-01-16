@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h2 class="text-2xl font-bold text-gray-800">
                    Créer une nouvelle évaluation
                </h2>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Nom de l'évaluation -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'évaluation
                    </label>
                    <input type="text" id="name" name="name" required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type d'évaluation (nouveau) -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type d'évaluation
                    </label>
                    <select id="type" name="type" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— Choisir le type —</option>
                        <option value="quiz">Quiz</option>
                        <option value="pdf">PDF / Document</option>
                        <option value="mcq">QCM (Questions à choix multiple)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload fichier (apparaît seulement si type = pdf) -->
                <div class="mb-6 hidden" id="pdf-upload">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Joindre le fichier (PDF recommandé)
                    </label>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Taille max recommandée : 5 Mo</p>
                </div>

                <!-- Date limite -->
                <div class="mb-6">
                    <label for="duedate" class="block text-sm font-medium text-gray-700 mb-2">
                        Date limite
                    </label>
                    <input type="datetime-local" id="duedate" name="duedate" required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('duedate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre de tentatives -->
                <div class="mb-6">
                    <label for="attemptnumber" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de tentatives autorisées
                    </label>
                    <input type="number" id="attemptnumber" name="attemptnumber" min="1" required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('attemptnumber')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Module (placeholder statique en attendant le backend) -->
                <div class="mb-6">
                    <label for="module_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Module associé
                    </label>
                    <select id="module_id" name="module_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner un module</option>
                        <option value="1">Mathématiques</option>
                        <option value="2">Informatique</option>
                        <option value="3">Français</option>
                        <!-- Ajoute tes modules réels ici quand le backend sera prêt -->
                    </select>
                    @error('module_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bouton Soumettre -->
                <div class="flex justify-end">
                    <button type="submit" id="submitBtn"
                            class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition flex items-center disabled:opacity-50">
                        <span id="btnText">Créer l'évaluation</span>
                        <svg id="spinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS: Afficher le champ fichier seulement si type = pdf -->
<script>
    const typeSelect = document.getElementById('type');
    const pdfGroup = document.getElementById('pdf-upload');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'pdf') {
            pdfGroup.classList.remove('hidden');
        } else {
            pdfGroup.classList.add('hidden');
        }
    });

    // Spinner pendant l'envoi
    document.getElementById('submitBtn').addEventListener('click', function() {
        this.disabled = true;
        document.getElementById('btnText').textContent = 'Création en cours...';
        document.getElementById('spinner').classList.remove('hidden');
    });
</script>
@endsection