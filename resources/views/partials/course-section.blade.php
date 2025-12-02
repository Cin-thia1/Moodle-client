{{--
    Ce template partiel affiche une seule section de cours.
    Il est inclus dans une boucle sur la page principale du cours.
--}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm">
    {{-- En-tête de la section (cliquable pour déplier/replier) --}}
    <h2>
        <button type="button" 
                class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-700 hover:bg-gray-50 rounded-t-lg focus:outline-none focus:ring-2 focus:ring-blue-200 info-panel-toggle"
                aria-expanded="false" 
                aria-controls="content-{{ $section->id }}">
            <span class="text-lg">{{ $section->name }}</span>
            <svg class="w-5 h-5 transform shrink-0 info-panel-icon transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </button>
    </h2>

    {{-- Contenu de la section (modules et formulaire d'ajout) --}}
    <div id="content-{{ $section->id }}" class="hidden overflow-hidden transition-all duration-300 ease-in-out">
        <div class="p-5 border-t border-gray-200 space-y-6">
            @forelse ($section->modules as $module)
                <div class="flex items-start p-4 space-x-4 bg-gray-50 rounded-md border-l-4 border-blue-500">
                    <div class="flex-shrink-0 pt-1">
                        @if ($module->modname == 'resource')
                            <i class="fas fa-file-alt text-xl text-blue-600"></i>
                        @elseif ($module->modname == 'assign')
                            <i class="fas fa-tasks text-xl text-orange-500"></i>
                        @else
                            <i class="fas fa-book-open text-xl text-gray-500"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $module->name }}</h3>
                        
                        @if ($module->modname == 'resource')
                            <p class="text-sm text-gray-500 mb-3">Ressource à télécharger.</p>
                            <a href="{{ route('module.download', $module->id) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>
                                Télécharger la ressource
                            </a>
                        @elseif ($module->modname == 'assign')
                            <div class="space-y-4 text-gray-700">
                                <div class="prose prose-sm max-w-none">
                                    {!! $module->intro ?? '<p class="text-gray-500">Aucune description disponible.</p>' !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">Aucun module dans cette section pour le moment.</div>
            @endforelse
            
            @if(Auth::user()->hasRole('ROLE_TEACHER'))
                <div class="pt-6 border-t border-gray-200 border-dashed">
                    <div class="text-center">
                        <button type="button" onclick="toggleModuleForm('{{ $section->id }}')"
                                class="inline-flex items-center px-4 py-2 border border-transparent bg-blue-100 text-blue-800 font-semibold rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter un module
                        </button>
                    </div>
                    <div id="module-form-{{ $section->id }}" class="hidden mt-6 max-w-2xl mx-auto p-6 bg-gray-50 rounded-lg border">
                        <h3 class="text-xl font-semibold mb-4 text-center text-gray-800">Créer un nouveau module</h3>
                        <form action="{{ route('modules.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $section->id }}">
                            <input type="hidden" name="downloadcontent" value="1">
                            <input type="hidden" name="modplural" id="modplural-{{ $section->id }}" value="Files">
                            <div>
                                <label for="name-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Nom du module</label>
                                <input type="text" name="name" id="name-{{ $section->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="modname-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Type de module</label>
                                <select name="modname" id="modname-{{ $section->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="updateModplural('{{ $section->id }}')">
                                    <option value="resource">Ressource (Fichier)</option>
                                    <option value="assign">Devoir</option>
                                </select>
                            </div>
                            <div>
                                <label for="file_path-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Fichier</label>
                                <input type="file" name="file_path" id="file_path-{{ $section->id }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div class="flex items-center justify-end space-x-3 pt-4">
                                <button type="button" onclick="toggleModuleForm('{{ $section->id }}')" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Annuler</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none">Créer le module</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif 
        </div>
    </div>
</div>