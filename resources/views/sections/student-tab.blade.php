<!-- Onglet Sections pour les Étudiants -->
<div id="sections" class="student-tab-content hidden">
    <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📚 Structure du cours</h2>

        @forelse($course->sections()->orderBy('id')->get() as $section)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-indigo-500 hover:shadow-xl transition">
                <div class="flex items-start gap-4">
                    <div class="text-3xl text-indigo-600">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $section->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-cube mr-1"></i>{{ $section->modules()->count() }} module(s)
                        </p>
                        @if($section->modules()->count() > 0)
                            <div class="mt-3 text-sm text-gray-600">
                                <p class="font-semibold mb-2">Contenu:</p>
                                <ul class="space-y-1 ml-4">
                                    @foreach($section->modules as $module)
                                        <li class="text-gray-600">
                                            <i class="fas fa-{{ $module->modname === 'resource' ? 'file' : ($module->modname === 'assign' ? 'tasks' : 'book') }} mr-2 text-gray-400"></i>
                                            {{ $module->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-layer-group text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-600">L'enseignant n'a pas encore défini la structure du cours</p>
            </div>
        @endforelse
    </div>
</div>
