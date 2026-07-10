<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

$courseId = 17;

$updateResult = $api->call('core_course_update_courses', [
    'courses' => [
        [
            'id' => $courseId,
            'overviewfiles' => [
                ['filename' => 'test.jpg', 'filepath' => '/', 'filecontent' => base64_encode('test')]
            ]
        ]
    ]
]);
echo "Update Result: " . json_encode($updateResult) . "\n";
