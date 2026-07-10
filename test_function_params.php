<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

try {
    $result = $api->call('local_course_add_new_course_module_resource', [
        'courseid' => 1,
        'section' => 1,
        'name' => 'Test',
        'intro' => 'Test intro',
        'itemid' => 0
    ]);
    var_dump($result);
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
    // Si MoodleApiService logged the debuginfo, let's check logs
}
