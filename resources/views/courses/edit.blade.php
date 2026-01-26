@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center">
    <h1 class="text-3xl font-bold">Editer un Cours: {{ $course->fullname }}</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />

    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <fieldset class="flex flex-col gap-4">
            <div class="flex flex-col justify-center">
                @if($course->image)
                <div class="w-36 mb-3 overflow-hidden">
                    <img src="{{ asset('storage/' . $course->image) }}" alt="Course cover" class="rounded-md" style="max-width: 100%; height: auto;">
                </div>
                @endif
                <div id="imagePreview" class="w-36 mb-3 overflow-hidden" style="display: none;">
                    <img id="previewImg" src="" alt="Image Preview" class="rounded-md" style="max-width: 100%; height: auto;">
                </div>
                <div class="flex items-center">
                    <label for="image" class="w-32">Image de couverture :</label>
                    <div class="relative">
                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" id="image" accept="image/*" onchange="previewImage(event)">
                        <button type="button" class='border border-primary py-1 px-2 text-primary rounded-md'>
                            Changer l'image
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center">
                <label for="fullname" class="w-36">Nom Complet :</label>
                <input type="text" name="fullname" class="py-1 rounded-md" id="fullname" value="{{ old('fullname', $course->fullname) }}" required>
            </div>
            <div class="flex items-center">
                <label for="shortname" class="w-36">Surnom :</label>
                <input type="text" name="shortname" class="py-1 rounded-md" id="shortname" value="{{ old('shortname', $course->shortname) }}" required>
            </div>
            <div class="flex items-center">
                <label for="summary" class="w-36">Résumé :</label>
                <textarea name="summary" class="py-1 rounded-md" id="summary">{{ old('summary', $course->summary) }}</textarea>
            </div>
            <div class="flex items-center">
                <label for="numsections" class="w-36">Nombre de Sections :</label>
                <input type="number" name="numsections" class="py-1 rounded-md" id="numsections" value="{{ old('numsections', $course->numsections) }}" required>
            </div>
            <div class="flex items-center">
                <label for="startdate" class="w-36">Date de début :</label>
                <input type="date" name="startdate" class="py-1 rounded-md" id="startdate" value="{{ old('startdate', $course->startdate) }}" required>
            </div>
            <div class="flex items-center">
                <label for="enddate" class="w-36">Date de fin :</label>
                <input type="date" name="enddate" class="py-1 rounded-md" id="enddate" value="{{ old('enddate', $course->enddate) }}">
            </div>
            <div class="flex items-center">
                <label for="teacher_id" class="w-36">Enseignant :</label>
                <input type="number" name="teacher_id" class="py-1 rounded-md" id="teacher_id" value="{{ old('teacher_id', $course->teacher_id) }}" required>
            </div>
        </fieldset>

        <x-button type="full" class="mt-6">
            Modifier Cours
        </x-button>
    </form>

    <!-- Delete Button -->
    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="mt-4">
        @csrf
        @method('DELETE')
        <x-button type="full" class="mt-6 bg-red-600 hover:bg-red-700">
            Supprimer Cours
        </x-button>
    </form>
</div>
@endsection

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
</script>
