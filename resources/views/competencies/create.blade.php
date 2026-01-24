@extends('layouts.app')

@section('title', 'Créer une compétence')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('competencies.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Créer une compétence</h1>

            <form action="{{ route('competencies.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="shortname" class="block text-sm font-semibold text-gray-700 mb-2">Nom court</label>
                    <input type="text" id="shortname" name="shortname" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shortname') border-red-500 @enderror"
                        value="{{ old('shortname') }}">
                    @error('shortname')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="idnumber" class="block text-sm font-semibold text-gray-700 mb-2">ID</label>
                    <input type="text" id="idnumber" name="idnumber" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('idnumber') border-red-500 @enderror"
                        value="{{ old('idnumber') }}">
                    @error('idnumber')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="1" selected>Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-check mr-2"></i> Créer
                    </button>
                    <a href="{{ route('competencies.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
