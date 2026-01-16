@props([
    'src' => asset('images/logo_moodle_client.png'),
    'alt' => 'moodle client'
])

<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes }}>