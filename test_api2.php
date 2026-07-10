<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

$draftInfo = $api->call('core_files_get_unused_draft_itemid', []);
$draftitemid = $draftInfo['itemid'];

$attempts = [
    ['courseid' => 17, 'section' => 1, 'name' => 'test', 'intro' => 'test', 'itemid' => $draftitemid],
    ['courseid' => 17, 'sectionid' => 1, 'name' => 'test', 'intro' => 'test', 'draftitemid' => $draftitemid],
    ['courseid' => 17, 'section' => 1, 'name' => 'test', 'intro' => 'test', 'draftitemid' => $draftitemid],
];

foreach ($attempts as $idx => $params) {
    echo "Attempt $idx with itemid $draftitemid:\n";
    try {
        $res = $api->call('local_course_add_new_course_module_resource', $params);
        echo "Success!\n";
        var_dump($res);
        break;
    } catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}
