<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

$draftInfo = $api->call('core_files_get_unused_draft_itemid', []);
$draftitemid = $draftInfo['itemid'];
echo "draft id is: " . $draftitemid . "\n";

try {
    $res = $api->call('local_course_add_new_course_module_resource', [
        'courseid' => 17,
        'section' => 1,
        'name' => 'test',
        'intro' => 'test',
        'itemid' => $draftitemid
    ]);
    var_dump($res);
} catch (\Exception $e) {
    echo "Ex 1: " . $e->getMessage() . "\n";
}

try {
    $res = $api->call('local_course_add_new_course_module_resource', [
        'courseid' => 17,
        'sectionid' => 1,
        'name' => 'test',
        'description' => 'test',
        'draftitemid' => $draftitemid
    ]);
    var_dump($res);
} catch (\Exception $e) {
    echo "Ex 2: " . $e->getMessage() . "\n";
}

try {
    $res = $api->call('local_course_add_new_course_module_resource', [
        'courseid' => 17,
        'section' => 1,
        'name' => 'test',
        'draftitemid' => $draftitemid
    ]);
    var_dump($res);
} catch (\Exception $e) {
    echo "Ex 3: " . $e->getMessage() . "\n";
}
