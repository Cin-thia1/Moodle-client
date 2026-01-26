<!-- Onglet Sections du Dashboard Enseignant -->
<div id="sections" class="tab-content hidden space-y-6">
    <div class="space-y-6">
        <!-- En-tête avec bouton d'ajout -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">📚 Sections du cours</h2>
                <p class="text-gray-600 mt-1">Définissez la structure de votre cours</p>
            </div>
            <a href="{{ route('sections.create', $course) }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm">
                <i class="fas fa-plus"></i> Nouvelle section
            </a>
        </div>

        <!-- Liste des sections -->
        <div class="bg-white rounded-lg shadow-lg border">
            @forelse($course->sections()->orderBy('id')->get() as $section)
                <div class="p-6 border-b last:border-b-0 hover:bg-gray-50 transition group">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                <i class="fas fa-layer-group text-indigo-600 mr-2"></i>{{ $section->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-cube mr-1"></i>{{ $section->modules()->count() }} module(s)
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a 
                                href="{{ route('sections.edit', [$course, $section]) }}" 
                                class="inline-flex items-center gap-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium"
                            >
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('sections.destroy', [$course, $section]) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr? Les modules de cette section seront supprimés.');">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center gap-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium"
                                >
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-600 mb-4">Aucune section pour le moment</p>
                    <a href="{{ route('sections.create', $course) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold">
                        <i class="fas fa-plus"></i> Créer la première section
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Conseils -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i> Conseils pour organiser vos sections
            </h4>
            <ul class="text-sm text-blue-800 space-y-2">
                <li>• Organisez vos sections de manière logique (par thème, par semaine, par compétence)</li>
                <li>• Donnez des noms clairs et descriptifs aux sections</li>
                <li>• Vous pourrez ajouter des modules et ressources dans chaque section</li>
                <li>• Les étudiants verront l'organisation que vous définissez</li>
            </ul>
        </div>
    </div>
</div>
