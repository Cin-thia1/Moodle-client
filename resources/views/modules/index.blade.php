@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modules</h1>
    {{-- <a href="{{ route('modules.create') }}" class="btn btn-primary mb-3">Créer un nouveau module</a> --}}
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Nom du module</th>
                <th>Pluralité</th>
                <th>Contenu téléchargeable</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $module)
            <tr>
                <td>{{ $module->id }}</td>
                <td>{{ $module->name }}</td>
                <td>{{ $module->modname }}</td>
                <td>{{ $module->modplural }}</td>
                <td>{{ $module->downloadcontent ? 'Oui' : 'Non' }}</td>
                <td>{{ $module->section->name ?? 'Aucune section' }}</td>
                <td>
                    <a href="{{ route('modules.show', $module) }}" class="btn btn-info btn-sm">Voir</a>
                    <a href="{{ route('modules.edit', $module) }}" class="btn btn-warning btn-sm">Modifier</a>
                    <form action="{{ route('modules.destroy', $module) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
