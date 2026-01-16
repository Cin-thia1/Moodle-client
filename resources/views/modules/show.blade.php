@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du module</h1>
    <p><strong>Nom :</strong> {{ $module->name }}</p>
    <p><strong>Nom du module :</strong> {{ $module->modname }}</p>
    <p><strong>Nom au pluriel :</strong> {{ $module->modplural }}</p>
    <p><strong>Contenu téléchargeable :</strong> {{ $module->downloadcontent ? 'Oui' : 'Non' }}</p>
    <p><strong>Chemin du fichier :</strong> {{ $module->file_path }}</p>
    <p><strong>Section :</strong> {{ $module->section->name ?? 'Aucune section' }}</p>
    <a href="{{ route('modules.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection
