@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">

            <!-- Mock Header -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Soumissions - Typologie des agents (CC1) – MOCK DATA
                    </h2>
                    <a href="#" class="text-blue-500 hover:text-blue-700 font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour (mock)
                    </a>
                </div>
            </div>

            <!-- Mock Summary Stats -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Total soumissions</p>
                        <p class="text-3xl font-bold text-gray-900">18</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">En attente</p>
                        <p class="text-3xl font-bold text-orange-600">7</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Corrigées</p>
                        <p class="text-3xl font-bold text-green-600">11</p>
                    </div>
                </div>
            </div>

            <!-- Mock Table -->
            <div class="p-6">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Étudiant</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date de soumission</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Mock Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">1</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">Marie Kengne</td>
                                <td class="px-6 py-5 text-sm text-gray-700">15 Juin 2025 - 14:30</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Corrigée
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-900">17.5 / 20</td>
                                <td class="px-6 py-5 text-sm">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                </td>
                            </tr>

                            <!-- Mock Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">2</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">Paul Kamga</td>
                                <td class="px-6 py-5 text-sm text-gray-700">16 Juin 2025 - 09:15</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        En attente
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">Non notée</td>
                                <td class="px-6 py-5 text-sm">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                </td>
                            </tr>

                            <!-- Mock Row 3 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-sm text-gray-900">3</td>
                                <td class="px-6 py-5 text-sm font-medium text-gray-900">Sophie Ngo</td>
                                <td class="px-6 py-5 text-sm text-gray-700">22 Juin 2025 - 10:00</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        En retard
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">Non notée</td>
                                <td class="px-6 py-5 text-sm">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i> Voir
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