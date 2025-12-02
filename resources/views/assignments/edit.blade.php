@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Assignment</h1>
    <x-form :action="route('assignments.update', $assignment)" method="PUT" :value="$assignment" buttonText="Update" :moduleId="$assignment->module_id" />
</div>
@endsection
