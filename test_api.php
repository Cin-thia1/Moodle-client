<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

$attempts = [
    ['courseid' => 17, 'section' => 1, 'name' => 'test', 'intro' => 'test', 'itemid' => 0],
    ['courseid' => 17, 'sectionid' => 1, 'name' => 'test', 'intro' => 'test', 'draftitemid' => 0],
    ['courseid' => 17, 'section' => 1, 'name' => 'test', 'intro' => 'test', 'draftitemid' => 0],
    ['courseid' => 17, 'section_number' => 1, 'name' => 'test', 'intro' => 'test', 'draftitemid' => 0],
];

foreach ($attempts as $idx => $params) {
    echo "Attempt $idx:\n";
    try {
        $res = $api->call('local_course_add_new_course_module_resource', $params);
        echo "Success!\n";
        var_dump($res);
        break;
    } catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}
