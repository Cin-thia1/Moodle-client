@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier le module</h1>
    <form action="{{ route('modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $module->name }}" required>
        </div>
        <div class="mb-3">
            <label for="modname" class="form-label">Nom du module</label>
            <input type="text" class="form-control" id="modname" name="modname" value="{{ $module->modname }}" required>
        </div>
        <div class="mb-3">
            <label for="modplural" class="form-label">Nom au pluriel</label>
            <input type="text" class="form-control" id="modplural" name="modplural" value="{{ $module->modplural }}" required>
        </div>
        <div class="mb-3">
            <label for="downloadcontent" class="form-label">Contenu téléchargeable</label>
            <select class="form-control" id="downloadcontent" name="downloadcontent">
                <option value="1" {{ $module->downloadcontent ? 'selected' : '' }}>Oui</option>
                <option value="0" {{ !$module->downloadcontent ? 'selected' : '' }}>Non</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="file_path" class="form-label">Chemin du fichier</label>
            <input type="text" class="form-control" id="file_path" name="file_path" value="{{ $module->file_path }}">
        </div>
        <div class="mb-3">
            <label for="section_id" class="form-label">Section</label>
            <select class="form-control" id="section_id" name="section_id">
                <option value="">Sélectionnez une section</option>
                @foreach ($sections as $section)
                <option value="{{ $section->id }}" {{ $module->section_id == $section->id ? 'selectionnée' : '' }}>{{ $section->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>
@endsection
