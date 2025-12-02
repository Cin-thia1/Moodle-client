<button 
    id="{{ $id ?? '' }}"
    type="{{ $type ?? '' }}"
    class="rounded-md py-2 px-4
        {{ isset($full) && $full === 'true' 
            ? 'text-white bg-blue-600 hover:bg-blue-700' 
            : 'text-blue-600 bg-white hover:bg-blue-100 border border-blue-600' }}
        {{ $class ?? '' }}">
    {{ $slot }}
</button>
