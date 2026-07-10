<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

// 1. Get unused draft id
$result = $api->call('core_files_get_unused_draft_itemid', []);
echo "Draft item ID: " . $result['itemid'] . "\n";
echo "Context ID: " . $result['contextid'] . "\n";

// 2. Upload file
$fileContent = base64_encode(file_get_contents('public/favicon.ico'));
$uploadResult = $api->callPost('core_files_upload', [
    'contextid' => $result['contextid'],
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $result['itemid'],
    'filepath' => '/',
    'filename' => 'favicon.ico',
    'filecontent' => $fileContent,
]);
print_r($uploadResult);

// 3. Update picture for user 2 (assuming 2 is a valid Moodle ID)
$updateResult = $api->call('core_user_update_picture', [
    'userid' => 2,
    'draftitemid' => $result['itemid'],
]);
var_dump($updateResult);
