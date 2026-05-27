<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = '148ed276a12b2dc4029cb38ad14d1c97';

try {
    $response = Illuminate\Support\Facades\Http::get('http://localhost/webservice/rest/server.php', [
        'wstoken' => $token,
        'wsfunction' => 'core_course_get_courses',
        'moodlewsrestformat' => 'json'
    ]);
    
    $data = $response->json();
    if (isset($data['exception'])) {
        echo "Exception: " . $data['message'] . "\n";
    } elseif (is_array($data)) {
        echo "Found " . count($data) . " courses.\n";
        foreach (array_slice($data, 0, 3) as $course) {
            echo " - " . ($course['fullname'] ?? 'no name') . " (ID: " . ($course['id'] ?? 'unknown') . ")\n";
        }
    } else {
        echo "Unexpected response: \n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
