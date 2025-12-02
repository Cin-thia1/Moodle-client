@extends('layouts.app')

@section('content')

<div class="container mx-auto w-4/5 py-8 bg-white rounded-2xl px-8">
    <!-- Bouton Retour -->
    <div class="p-2">
        <a href="{{ url()->previous() }}" 
           class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
    
    <!-- En-tête avec icône d'assignment -->
    <div class="flex items-center mb-6">
        <div class="bg-purple-100 p-3 rounded-full mr-4">
            <i class="fas fa-clipboard-list text-purple-600 text-2xl"></i>
        </div>
        <div>
            <h1 class="font-bold text-3xl text-gray-800">{{ $module->name ?? 'cc 1' }}</h1>
            <p class="text-gray-600 mt-1">Gestion des soumissions</p>
        </div>
    </div>
    
    <hr class="mb-6 bg-gray-300 h-[1px]">

    <!-- Informations du devoir -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6 border border-blue-200">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Date d'ouverture</p>
                <p class="font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                    Samedi, 14 Juin 2025, 12:00 AM
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Date limite</p>
                <p class="font-semibold text-gray-800">
                    <i class="fas fa-clock text-orange-600 mr-2"></i>
                    Samedi, 21 Juin 2025, 12:00 AM
                </p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-sm text-gray-600 mb-1">Description du devoir</p>
            <p class="text-gray-800">C'est un premier cc pour tester votre maîtrise de la typologie des agents</p>
        </div>
    </div>

    <!-- Résumé de notation -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                Résumé de notation
            </h2>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-600">Participants</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-paper-plane text-green-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-600">Soumis</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-600">À corriger</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-purple-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">6 jours 18h</p>
                    <p class="text-sm text-gray-600">Temps restant</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    {{-- <div class="grid md:grid-cols-3 gap-4 mb-6">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg flex items-center justify-center">
            <i class="fas fa-star mr-2"></i>
            Noter
        </button>
        
        <button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg flex items-center justify-center">
            <i class="fas fa-cog mr-2"></i>
            Paramètres
        </button>
        
        <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            Ajouter soumission
        </button>
    </div> --}}

    <!-- Liste des soumissions -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-blue-600 mr-3"></i>
                Soumissions des étudiants
            </h2>
        </div>
        
        <div class="p-8">
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-3">Aucune soumission disponible</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Les étudiants n'ont pas encore soumis leurs devoirs. Les soumissions apparaîtront ici une fois qu'elles seront reçues.
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-lg mx-auto">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="text-left">
                            <p class="text-sm font-medium text-blue-800 mb-1">Information</p>
                            <p class="text-sm text-blue-700">
                                Les étudiants ont encore <strong>6 jours et 18 heures</strong> pour soumettre leurs travaux.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exemple de ce à quoi ressembleraient les soumissions (commenté) -->
    <!--
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mt-4">
        <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">John Doe</p>
                        <p class="text-sm text-gray-600">Soumis le 15 Juin 2025, 14:30</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        À l'heure
                    </span>
                    <button class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-eye mr-1"></i> Voir
                    </button>
                </div>
            </div>
        </div>
    </div>
    -->
</div>

@endsection