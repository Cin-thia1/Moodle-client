<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

// 1. Get unused draft id
$result = $api->call('core_files_get_unused_draft_itemid', []);
$itemid = $result['itemid'];
$contextid = $result['contextid'];

// 2. Upload file
file_put_contents('test.txt', 'Hello World!');
$fileContent = base64_encode(file_get_contents('test.txt'));
$uploadResult = $api->callPost('core_files_upload', [
    'contextid' => $contextid,
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $itemid,
    'filepath' => '/',
    'filename' => 'test.txt',
    'filecontent' => $fileContent,
]);

// 3. Let's see if Moodle returns the URL
$url = $uploadResult['url'] ?? '';
echo "Draft URL: $url\n";

// Moodle needs authentication to download from draftfile.php, so we can't easily curl it without a cookie.
// Let's just create a real image, upload it, and update the picture to see if it works for user 2.
file_put_contents('test.jpg', base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=')); // 1x1 white jpeg

$fileContent = base64_encode(file_get_contents('test.jpg'));
$api->callPost('core_files_upload', [
    'contextid' => $contextid,
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $itemid,
    'filepath' => '/',
    'filename' => 'test.jpg',
    'filecontent' => $fileContent,
]);

$updateResult = $api->call('core_user_update_picture', [
    'userid' => 2,
    'draftitemid' => $itemid,
]);
var_dump($updateResult);

