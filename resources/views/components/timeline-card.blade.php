<hr class="w-full h-[2px] mt-2 mb-4 bg-gray-300" />
<div class="w-full p-1 flex flex-col gap-4">
    <h2 class="font-bold text-xl">{{ $date }}</h2>
    <div class="w-full flex justify-between items-center gap-8">
        <div class="flex gap-4 items-center">
            <div>{{ $time }}</div>
            <div>
                <i class="fa-regular fa-file-lines text-3xl"></i>
            </div>
        </div>
        <div class="w-full flex flex-col items-start">
            <span class="font-bold">{{ $title }}</span>
            <span class="text-gray-500">{{ $description }}</span>
        </div>
        <x-button class="text-nowrap">{{ $action }}</x-button>
    </div>
</div>
