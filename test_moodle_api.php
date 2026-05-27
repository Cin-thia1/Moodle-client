<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);
try {
    $courses = $api->call('core_course_get_courses', []);
    echo "Courses count: " . count($courses) . "\n";
    if (count($courses) > 0) {
        echo "First course: " . $courses[0]['fullname'] . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
