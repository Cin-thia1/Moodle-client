@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Créer une nouvelle évaluation</h2>
            </div>

            <form action="{{ route('assignments.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'évaluation</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="duedate" class="block text-sm font-medium text-gray-700">Date limite</label>
                        <input type="datetime-local" id="duedate" name="duedate" class="mt-1 block w-full" required>
                    </div>


                    <div class="mb-4">
                        <label for="attemptnumber" class="block text-sm font-medium text-gray-700">Nombre de tentatives</label>
                        <input type="number" id="attemptnumber" name="attemptnumber" min="1" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="module_id" class="block text-sm font-medium text-gray-700">Module</label>
                        <select id="module_id" name="module_id" class="mt-1 block w-full" required>
                            <option value="">Sélectionner un module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sélectionner des questions existantes -->
                    <div class="mb-4">
                        <label for="questions" class="block text-sm font-medium text-gray-700">Questions existantes</label>
                        <select id="questions" name="questions[]" multiple class="mt-1 block w-full">
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}">{{ $question->content }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white hover:bg-blue-700 px-4 py-2 rounded">
                            Créer l'évaluation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
