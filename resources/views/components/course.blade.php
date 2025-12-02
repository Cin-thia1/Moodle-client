<div class="rounded-lg overflow-hidden border border-primary/20 shadow-md">
    <a class="cursor-pointer" href="{{ route('courses.show', $course) }}">
        <div class="h-24 overflow-hidden">
            <img src="{{ $course->img ?? 'images/mathematics.jpeg' }}" alt="{{ $course->fullname ?? 'No name' }} image" />
        </div>
        <div class="h-20 p-2 py-0">
            <div class="font-bold text-xl flex justify-between">
                <span>{{ $course->fullname ?? 'No name' }}</span>
            </div>
            <div class="italic text-base">{{ $course->category->name ?? 'No category' }}</div>
        </div>
    </a>
</div>
