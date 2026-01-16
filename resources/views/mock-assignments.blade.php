@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">

            <!-- Mock Header -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Mes Énoncés d'Évaluation – MOCK DATA
                    </h2>
                    <a href="#" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center">
                        <i class="fas fa-plus mr-2"></i> Nouvel Énoncé
                    </a>
                </div>
            </div>

            <!-- Mock Tabs (like your index) -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-center space-x-6">
                    <a href="#" class="text-lg font-medium px-4 py-2 rounded-lg bg-gray-100 text-blue-600 border-b-2 border-blue-600">
                        <i class="fas fa-file-alt mr-2"></i> Énoncés
                    </a>
                    <a href="#" class="text-lg font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition text-gray-600">
                        <i class="fas fa-paper-plane mr-2"></i> Soumissions
                    </a>
                </div>
            </div>

            <!-- Mock Assignments Table -->
            <div class="p-6">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date limite</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Mock Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">1</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">CC1 - Typologie des agents</td>
                                <td class="px-6 py-5 text-sm text-gray-700">PDF</td>
                                <td class="px-6 py-5 text-sm text-gray-700">21 Juin 2025</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                    <a href="#" class="text-yellow-600 hover:text-yellow-800 mr-3">
                                        <i class="fas fa-edit mr-1"></i> Modifier
                                    </a>
                                    <a href="#" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash mr-1"></i> Supprimer
                                    </a>
                                </td>
                            </tr>

                            <!-- Mock Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">2</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">Quiz - Histoire du Cameroun</td>
                                <td class="px-6 py-5 text-sm text-gray-700">Quiz</td>
                                <td class="px-6 py-5 text-sm text-gray-700">30 Juin 2025</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Brouillon
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                    <a href="#" class="text-yellow-600 hover:text-yellow-800 mr-3">
                                        <i class="fas fa-edit mr-1"></i> Modifier
                                    </a>
                                    <a href="#" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash mr-1"></i> Supprimer
                                    </a>
                                </td>
                            </tr>

                            <!-- Mock Row 3 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">3</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">QCM - Économie publique</td>
                                <td class="px-6 py-5 text-sm text-gray-700">QCM</td>
                                <td class="px-6 py-5 text-sm text-gray-700">05 Juil 2025</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                    <a href="#" class="text-yellow-600 hover:text-yellow-800 mr-3">
                                        <i class="fas fa-edit mr-1"></i> Modifier
                                    </a>
                                    <a href="#" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash mr-1"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection