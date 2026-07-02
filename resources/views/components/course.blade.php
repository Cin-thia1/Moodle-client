<div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1.5 overflow-hidden flex flex-col h-full group">
    <a class="cursor-pointer flex flex-col h-full justify-between" href="{{ route('courses.show', $course) }}">
        
        <!-- Image Area -->
        <div class="h-44 overflow-hidden bg-gray-100 relative shrink-0">
            <img src="{{ \App\Helpers\ImageHelper::getCourseImageUrl($course->image) }}" alt="{{ $course->fullname ?? 'No name' }} image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
            
            <!-- Sync Status Badge -->
            <div class="absolute top-3 right-3 z-10">
                @if($course->dirty === 1)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500 text-white shadow-sm backdrop-blur-md">
                        <i class="fas fa-spinner animate-spin mr-1"></i> Modifié
                    </span>
                @elseif($course->sync_status === 'pending')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-500 text-white shadow-sm backdrop-blur-md">
                        <i class="fas fa-hourglass-half mr-1"></i> En attente
                    </span>
                @elseif($course->sync_status === 'synced')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500 text-white shadow-sm backdrop-blur-md">
                        <i class="fas fa-check mr-1"></i> Synchronisé
                    </span>
                @elseif($course->sync_status === 'conflict')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500 text-white shadow-sm backdrop-blur-md">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Conflit
                    </span>
                @endif
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div>
                <!-- Category and Shortname Row -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 tracking-wide uppercase border border-indigo-100/60 truncate max-w-[150px]">
                        {{ $course->category->name ?? 'Sans catégorie' }}
                    </span>
                    @if($course->shortname)
                        <span class="text-[10px] font-mono text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-200/60 truncate max-w-[100px]" title="{{ $course->shortname }}">
                            {{ $course->shortname }}
                        </span>
                    @endif
                </div>

                <!-- Title -->
                <h3 class="text-base font-bold text-gray-900 leading-snug mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors duration-200">
                    {{ $course->fullname ?? 'Sans nom' }}
                </h3>

                <!-- Summary / Description -->
                @if($course->summary)
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                        {{ strip_tags($course->summary) }}
                    </p>
                @else
                    <p class="text-xs text-gray-400 italic line-clamp-2 mb-4 leading-relaxed">
                        Aucune description disponible pour ce cours.
                    </p>
                @endif
            </div>

            <div>
                <!-- Teacher Section -->
                <div class="flex items-center gap-2.5 pt-3.5 border-t border-gray-100 mb-3.5">
                    @php
                        $hasTeacher = $course->teacher;
                        $initials = $hasTeacher ? strtoupper(substr($course->teacher->name, 0, 2)) : 'NA';
                        $avatarUrl = $hasTeacher && $course->teacher->avatar ? $course->teacher->avatar : null;
                    @endphp
                    <div class="h-8 w-8 rounded-full bg-indigo-50 border border-indigo-100/80 text-indigo-600 font-bold text-xs flex items-center justify-center overflow-hidden shrink-0">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover">
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[9px] text-gray-400 uppercase font-bold tracking-wider leading-none">Enseignant</span>
                        <span class="text-xs font-semibold text-gray-700 truncate leading-normal mt-0.5">{{ $course->teacher->name ?? 'Non assigné' }}</span>
                    </div>
                </div>

                <!-- Metrics Footer -->
                <div class="flex items-center justify-between text-[11px] text-gray-500 font-medium">
                    <span class="flex items-center gap-1.5" title="Nombre d'étudiants inscrits">
                        <i class="fa-solid fa-users text-gray-400 w-3.5"></i>
                        <span>{{ $course->students()->count() }} étudiants</span>
                    </span>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1" title="Documents de cours">
                            <i class="fa-solid fa-file-lines text-gray-400 w-3.5"></i>
                            <span>{{ $course->documents()->count() }}</span>
                        </span>
                        <span class="flex items-center gap-1" title="Sections du cours">
                            <i class="fa-solid fa-folder text-gray-400 w-3.5"></i>
                            <span>{{ $course->sections()->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
    </a>
</div>

