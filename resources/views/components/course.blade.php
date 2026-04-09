<div class="rounded-lg overflow-hidden border border-primary/20 shadow-md hover:shadow-lg transition">
    <a class="cursor-pointer" href="{{ route('courses.show', $course) }}">
        <div class="h-24 overflow-hidden bg-gray-200 relative">
            @if($course->image && \Storage::disk('public')->exists($course->image))
                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->fullname ?? 'No name' }} image" class="w-full h-full object-cover" />
            @else
                <img src="{{ asset('images/mathematics.jpeg') }}" alt="{{ $course->fullname ?? 'No name' }} image" class="w-full h-full object-cover" />
            @endif
            <!-- Badge de statut de sync -->
            <div class="absolute top-2 right-2">
                @if($course->dirty === 1)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-spinner animate-spin mr-1"></i> Modifié
                    </span>
                @elseif($course->sync_status === 'pending')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-hourglass-half mr-1"></i> En attente
                    </span>
                @elseif($course->sync_status === 'synced')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i> Synchronisé
                    </span>
                @elseif($course->sync_status === 'conflict')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Conflit
                    </span>
                @endif
            </div>
        </div>
        <div class="h-20 p-2 py-0">
            <div class="font-bold text-xl flex justify-between">
                <span>{{ $course->fullname ?? 'No name' }}</span>
            </div>
            <div class="italic text-base">{{ $course->category->name ?? 'No category' }}</div>
        </div>
    </a>
</div>
