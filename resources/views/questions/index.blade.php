@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Liste des Questions</h3>

                    <table class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Contenu</th>
                                @if(auth()->user()->hasRole('ROLE_TEACHER'))
                                 <th class="px-4 py-2 text-left">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr>
                                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-4 py-2">{{ $question->content }}</td>
                                    @if(auth()->user()->hasRole('ROLE_TEACHER'))
                                        <td class="border px-4 py-2">
                                        
                                                <a href="{{ route('questions.edit', $question->id) }}" class="text-blue-500 hover:text-blue-700">Modifier</a>
                                        
                                            
                                                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                                        Supprimer
                                                    </button>
                                                </form>
                                    
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(auth()->user()->hasRole('ROLE_TEACHER'))
                        <div class="mt-4">
                            <a href="{{ route('questions.create') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">Ajouter une Question</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
