@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">{{ $module->name }}</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Instructions</h2>
        <div class="prose max-w-none">
            {!! $module->activity !!}
        </div>
    </div>

    @if($module->pdf_url)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Document associé</h2>
        <div class="border rounded-lg p-4 flex items-center justify-between">
            <div>
                <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                <span>{{ $module->pdf_filename }}</span>
            </div>
            <a href="{{ $module->pdf_url }}" target="_blank" 
            class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-download"></i> Télécharger
            </a>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasRole('ROLE_TEACHER'))
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Composer une épreuve</h2>
        
        <form action="{{ route('assignments.compose', $module) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Sélectionnez les fichiers</label>
                {{-- @foreach($module->assignmentFiles as $file)
                <div class="flex items-center mb-2">
                    <input type="checkbox" name="selected_files[]" 
                           value="{{ $file->id }}" 
                           id="file_{{ $file->id }}" class="mr-2">
                    <label for="file_{{ $file->id }}">{{ $file->filename }}</label>
                </div>
                @endforeach --}}
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Instructions supplémentaires</label>
                <textarea name="instructions" rows="4" 
                          class="w-full border rounded-lg p-2"></textarea>
            </div>
            
            <button type="submit" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Créer l'épreuve
            </button>
        </form>
        
        <hr class="my-6">
        
        <h2 class="text-xl font-semibold mb-4">Attribuer une note</h2>
        <form action="{{ route('grades.create', $module) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Note (0-100)</label>
                <input type="number" name="grade" min="0" max="100" 
                       class="border rounded-lg p-2 w-24">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Commentaire</label>
                <textarea name="comment" rows="3" 
                          class="w-full border rounded-lg p-2"></textarea>
            </div>
            
            <input type="hidden" name="submission_id" value="1"> <!-- À adapter -->
            
            <button type="submit" 
                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                Enregistrer la note
            </button>
        </form>
    </div>
    @endif
</div>
@endsection