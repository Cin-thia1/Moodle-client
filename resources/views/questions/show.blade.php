@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Détails de la Question</h3>

                    <div class="mb-4">
                        <strong>Contenu :</strong> <p>{{ $question->content }}</p>
                    </div>

                    <div class="mb-4">
                        <strong>Propositions :</strong>
                        <ul class="list-disc pl-5">
                            @foreach (json_decode($question->choices) as $index => $choice)
                                <li class="{{ $question->correct_choice_id == $index ? 'font-bold text-green-600' : '' }}">
                                    {{ chr(65 + $index) }}. {{ $choice }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-4">
                        <strong>Réponse correcte :</strong> {{ chr(65 + $question->correct_choice_id) }}.
                    </div>

                    @can('update', $question)
                        <a href="{{ route('questions.edit', $question->id) }}" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600">Modifier</a>
                    @endcan

                    @can('delete', $question)
                        <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mt-4" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                Supprimer
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
