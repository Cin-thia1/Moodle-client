@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center bg-white rounded-2xl p-6 shadow-sm">
    <div class="p-6 pb-2">
        <a href="{{ url()->previous() }}" class="text-blue-500 hover:text-blue-700 font-medium inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
    <h1 class='text-3xl font-bold mb-4 px-6'>Editer un Cours: {{ $course->fullname }}</h1>
    <hr class="w-full h-[2px] mb-6 bg-gray-200 border-0" />

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-6">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">Il y a des erreurs dans votre formulaire.</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 pb-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="updated_at" value="{{ $course->updated_at ? $course->updated_at->toDateTimeString() : '' }}">
        
        <!-- SECTION: GENERAL (Always visible, not in an accordion) -->
        <fieldset class="border border-gray-200 rounded-md p-4 bg-gray-50">
            <legend class="text-lg font-semibold text-gray-700 px-2">Général</legend>
            <div class="flex flex-col gap-4 mt-2">
                <!-- Nom complet -->
                <div class="flex items-center">
                    <label for="fullname" class="w-1/4 font-medium text-gray-700">Nom complet du cours <span class="text-red-500">*</span> :</label>
                    <input type="text" name="fullname" id="fullname" value="{{ old('fullname', $course->fullname) }}" class="py-2 px-3 border border-gray-300 rounded-md w-3/4 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>

                <!-- Surnom -->
                <div class="flex items-center">
                    <label for="shortname" class="w-1/4 font-medium text-gray-700">Nom abrégé du cours <span class="text-red-500">*</span> :</label>
                    <input type="text" name="shortname" id="shortname" value="{{ old('shortname', $course->shortname) }}" class="py-2 px-3 border border-gray-300 rounded-md w-3/4 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>

                <!-- Catégorie -->
                <div class="flex items-center">
                    <label for="category_id" class="w-1/4 font-medium text-gray-700">Catégorie de cours <span class="text-red-500">*</span> :</label>
                    <select name="category_id" id="category_id" class="py-2 px-3 border border-gray-300 rounded-md w-3/4 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Visibilité -->
                <div class="flex items-center">
                    <label for="visible" class="w-1/4 font-medium text-gray-700">Visibilité du cours :</label>
                    <select name="visible" id="visible" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="1" {{ old('visible', $course->visible) == 1 ? 'selected' : '' }}>Afficher</option>
                        <option value="0" {{ old('visible', $course->visible) == 0 ? 'selected' : '' }}>Masquer</option>
                    </select>
                </div>

                <!-- Date de début -->
                <div class="flex items-center">
                    <label for="startdate" class="w-1/4 font-medium text-gray-700">Date de début du cours <span class="text-red-500">*</span> :</label>
                    <input type="date" name="startdate" id="startdate" value="{{ old('startdate', $course->startdate ? $course->startdate->format('Y-m-d') : '') }}" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>

                <!-- Date de fin -->
                <div class="flex items-center">
                    <label for="enddate" class="w-1/4 font-medium text-gray-700">Date de fin du cours :</label>
                    <div class="w-3/4 flex items-center gap-2">
                        <input type="date" name="enddate" id="enddate" value="{{ old('enddate', $course->enddate ? $course->enddate->format('Y-m-d') : '') }}" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span class="text-sm text-gray-500">(Optionnel)</span>
                    </div>
                </div>

                <!-- N d'identification -->
                <div class="flex items-center">
                    <label for="idnumber" class="w-1/4 font-medium text-gray-700">N° d'identification du cours :</label>
                    <input type="text" name="idnumber" id="idnumber" value="{{ old('idnumber', $course->idnumber) }}" class="py-2 px-3 border border-gray-300 rounded-md w-3/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </fieldset>

        <!-- ACCORDIONS -->
        <div class="border border-gray-200 rounded-md divide-y divide-gray-200">
            
            <!-- SECTION: DESCRIPTION -->
            <details class="group bg-white" open>
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Description</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-top">
                        <label for="summary" class="w-1/4 font-medium text-gray-700 mt-2">Résumé du cours :</label>
                        <textarea name="summary" id="summary" rows="5" class="py-2 px-3 border border-gray-300 rounded-md w-3/4 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Description optionnelle...">{{ old('summary', $course->summary) }}</textarea>
                    </div>

                    <div class="flex items-top">
                        <label for="image" class="w-1/4 font-medium text-gray-700 mt-2">Image du cours :</label>
                        <div class="w-3/4">
                            @if($course->image)
                            <div class="w-48 mb-4 border border-gray-200 p-2 rounded bg-gray-50">
                                <p class="text-sm text-gray-500 mb-2">Image actuelle :</p>
                                <img src="{{ \App\Helpers\ImageHelper::getCourseImageUrl($course->image) }}" alt="Course cover" class="max-w-full h-auto rounded">
                            </div>
                            @endif
                            
                            <div class="flex items-center gap-4">
                                <label class="border border-gray-300 py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md cursor-pointer transition">
                                    <span>Changer l'image...</span>
                                    <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                                </label>
                                <span id="fileName" class="text-sm text-gray-500">Aucun nouveau fichier</span>
                            </div>
                            <div id="imagePreview" class="mt-4 border border-gray-200 p-2 rounded bg-gray-50 w-48 hidden">
                                <p class="text-sm text-gray-500 mb-2">Nouvelle image :</p>
                                <img id="previewImg" src="" alt="Aperçu" class="max-w-full h-auto rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <!-- SECTION: FORMAT DU COURS -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Format du cours</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="format" class="w-1/4 font-medium text-gray-700">Format :</label>
                        <select name="format" id="format" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="topics" {{ old('format', $course->format) == 'topics' ? 'selected' : '' }}>Thématique (Topics)</option>
                            <option value="weeks" {{ old('format', $course->format) == 'weeks' ? 'selected' : '' }}>Hebdomadaire (Weekly)</option>
                            <option value="social" {{ old('format', $course->format) == 'social' ? 'selected' : '' }}>Informel (Social)</option>
                            <option value="singleactivity" {{ old('format', $course->format) == 'singleactivity' ? 'selected' : '' }}>Activité unique</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="numsections" class="w-1/4 font-medium text-gray-700">Nombre de sections :</label>
                        <input type="number" name="numsections" id="numsections" min="0" max="52" value="{{ old('numsections', $course->numsections) }}" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    </div>

                    <div class="flex items-center">
                        <label for="hiddensections" class="w-1/4 font-medium text-gray-700">Sections cachées :</label>
                        <select name="hiddensections" id="hiddensections" class="py-2 px-3 border border-gray-300 rounded-md w-1/2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="0" {{ old('hiddensections', $course->hiddensections) == 0 ? 'selected' : '' }}>Les sections cachées sont invisibles</option>
                            <option value="1" {{ old('hiddensections', $course->hiddensections) == 1 ? 'selected' : '' }}>Les sections cachées sont affichées sous forme condensée</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="coursedisplay" class="w-1/4 font-medium text-gray-700">Mise en page du cours :</label>
                        <select name="coursedisplay" id="coursedisplay" class="py-2 px-3 border border-gray-300 rounded-md w-1/2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="0" {{ old('coursedisplay', $course->coursedisplay) == 0 ? 'selected' : '' }}>Afficher toutes les sections sur une page</option>
                            <option value="1" {{ old('coursedisplay', $course->coursedisplay) == 1 ? 'selected' : '' }}>Afficher une section par page</option>
                        </select>
                    </div>
                </div>
            </details>

            <!-- SECTION: APPARENCE -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Apparence</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="lang" class="w-1/4 font-medium text-gray-700">Imposer la langue :</label>
                        <select name="lang" id="lang" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Ne pas imposer (Défaut)</option>
                            <option value="fr" {{ old('lang', $course->lang) == 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="en" {{ old('lang', $course->lang) == 'en' ? 'selected' : '' }}>Anglais</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="newsitems" class="w-1/4 font-medium text-gray-700">Nombre d'annonces :</label>
                        <input type="number" name="newsitems" id="newsitems" min="0" max="10" value="{{ old('newsitems', $course->newsitems) }}" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center">
                        <label for="showgrades" class="w-1/4 font-medium text-gray-700">Afficher le carnet de notes :</label>
                        <select name="showgrades" id="showgrades" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('showgrades', $course->showgrades) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('showgrades', $course->showgrades) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="showreports" class="w-1/4 font-medium text-gray-700">Afficher les rapports d'activité :</label>
                        <select name="showreports" id="showreports" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('showreports', $course->showreports) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('showreports', $course->showreports) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="showactivitydates" class="w-1/4 font-medium text-gray-700">Afficher les dates d'activité :</label>
                        <select name="showactivitydates" id="showactivitydates" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('showactivitydates', $course->showactivitydates) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('showactivitydates', $course->showactivitydates) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>
                </div>
            </details>

            <!-- SECTION: FICHIERS ET ENVOIS -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Fichiers et envois</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="maxbytes" class="w-1/4 font-medium text-gray-700">Taille maximale des fichiers déposés :</label>
                        <select name="maxbytes" id="maxbytes" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="0" {{ old('maxbytes', $course->maxbytes) == 0 ? 'selected' : '' }}>Limite du site</option>
                            <option value="104857600" {{ old('maxbytes', $course->maxbytes) == 104857600 ? 'selected' : '' }}>100 Mo</option>
                            <option value="52428800" {{ old('maxbytes', $course->maxbytes) == 52428800 ? 'selected' : '' }}>50 Mo</option>
                            <option value="10485760" {{ old('maxbytes', $course->maxbytes) == 10485760 ? 'selected' : '' }}>10 Mo</option>
                            <option value="2097152" {{ old('maxbytes', $course->maxbytes) == 2097152 ? 'selected' : '' }}>2 Mo</option>
                        </select>
                    </div>
                </div>
            </details>

            <!-- SECTION: SUIVI D'ACHÈVEMENT -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Suivi d'achèvement</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="enablecompletion" class="w-1/4 font-medium text-gray-700">Activer le suivi d'achèvement :</label>
                        <select name="enablecompletion" id="enablecompletion" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('enablecompletion', $course->enablecompletion) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('enablecompletion', $course->enablecompletion) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="showcompletionconditions" class="w-1/4 font-medium text-gray-700">Afficher les conditions d'achèvement :</label>
                        <select name="showcompletionconditions" id="showcompletionconditions" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('showcompletionconditions', $course->showcompletionconditions) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('showcompletionconditions', $course->showcompletionconditions) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>
                </div>
            </details>

            <!-- SECTION: GROUPES -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600">
                    <span class="text-lg">Groupes</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="groupmode" class="w-1/4 font-medium text-gray-700">Mode de groupe :</label>
                        <select name="groupmode" id="groupmode" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="0" {{ old('groupmode', $course->groupmode) == 0 ? 'selected' : '' }}>Pas de groupes</option>
                            <option value="1" {{ old('groupmode', $course->groupmode) == 1 ? 'selected' : '' }}>Groupes séparés</option>
                            <option value="2" {{ old('groupmode', $course->groupmode) == 2 ? 'selected' : '' }}>Groupes visibles</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <label for="groupmodeforce" class="w-1/4 font-medium text-gray-700">Imposer le mode de groupe :</label>
                        <select name="groupmodeforce" id="groupmodeforce" class="py-2 px-3 border border-gray-300 rounded-md w-1/4 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1" {{ old('groupmodeforce', $course->groupmodeforce) == 1 ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('groupmodeforce', $course->groupmodeforce) == 0 ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <label for="defaultgroupingid" class="w-1/4 font-medium text-gray-700">Groupement par défaut :</label>
                        <select name="defaultgroupingid" id="defaultgroupingid" class="py-2 px-3 border border-gray-300 rounded-md w-1/3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="0" {{ old('defaultgroupingid', $course->defaultgroupingid) == 0 ? 'selected' : '' }}>Aucun</option>
                            <!-- Plus d'options seront rajoutées dynamiquement s'il y a des groupements -->
                        </select>
                    </div>
                </div>
            </details>

            <!-- SECTION: TAGS -->
            <details class="group bg-white">
                <summary class="flex justify-between items-center font-semibold cursor-pointer list-none p-4 hover:bg-gray-50 text-blue-600 rounded-b-md">
                    <span class="text-lg">Tags</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="p-4 bg-white border-t border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="tags" class="w-1/4 font-medium text-gray-700">Saisir les tags :</label>
                        <div class="w-3/4">
                            <input type="text" name="tags" id="tags" value="{{ old('tags', $course->tags) }}" class="py-2 px-3 border border-gray-300 rounded-md w-full focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="tag1, tag2, tag3...">
                            <p class="text-sm text-gray-500 mt-1">Séparez les tags par des virgules.</p>
                        </div>
                    </div>
                </div>
            </details>
        </div>

        <div class="flex justify-center gap-4 mt-8 pt-4 border-t border-gray-200">
            <x-button full="true" type="submit" class="px-8 py-2 text-lg">
                Mettre à jour
            </x-button>
            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-8 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 text-lg">
                Annuler
            </a>
        </div>
    </form>

    <div class="px-6 pb-6 mt-4 border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-red-600 mb-2">Zone de danger</h3>
        <p class="text-gray-600 text-sm mb-4">Une fois supprimé, ce cours et toutes ses données associées seront perdus de façon permanente.</p>
        
        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce cours définitivement ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-trash mr-2"></i> Supprimer le cours
            </button>
        </form>
    </div>
</div>

<style>
    /* Permet de cacher la flèche par défaut de details/summary sous Chrome/Safari et Firefox */
    details > summary {
        list-style: none;
    }
    details > summary::-webkit-details-marker {
        display: none;
    }
</style>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const fileNameElement = document.getElementById('fileName');
        
        if (file) {
            fileNameElement.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            fileNameElement.textContent = 'Aucun fichier sélectionné';
            document.getElementById('imagePreview').classList.add('hidden');
        }
    }
</script>
@endsection
