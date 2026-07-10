<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

// Trouver une vraie photo de profil existante
$user = \App\Models\User::where('moodle_id', 2)->first();
echo "User: " . ($user ? $user->name : "not found") . "\n";
echo "Profile picture: " . ($user ? $user->profile_picture : "none") . "\n";

// Lister les photos de profil dispo
$files = glob(storage_path('app/public/profile_pictures/*'));
echo "\nPhotos disponibles:\n";
foreach ($files as $f) {
    echo "  " . $f . " (" . filesize($f) . " bytes, type: " . mime_content_type($f) . ")\n";
}

// Tester avec la première photo trouvée
if (empty($files)) {
    echo "Aucune photo trouvée\n";
    exit;
}

$testFile = $files[0];
$filename = basename($testFile);
$mime = mime_content_type($testFile);
echo "\nTest avec: $testFile\n";

// 1. Get draft
$draftInfo = $api->call('core_files_get_unused_draft_itemid', []);
echo "Draft itemid: " . $draftInfo['itemid'] . ", contextid: " . $draftInfo['contextid'] . "\n";

// 2. Upload avec le bon mime type comme extension
$ext = match($mime) {
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/gif' => '.gif',
    default => '.jpg',
};
$uploadFilename = 'profile' . $ext;

$uploadResult = $api->callPost('core_files_upload', [
    'contextid' => $draftInfo['contextid'],
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $draftInfo['itemid'],
    'filepath' => '/',
    'filename' => $uploadFilename,
    'filecontent' => base64_encode(file_get_contents($testFile)),
]);

echo "Upload result: " . json_encode($uploadResult) . "\n";

// 3. Update picture
$updateResult = $api->call('core_user_update_picture', [
    'userid' => 2,
    'draftitemid' => $draftInfo['itemid'],
]);

echo "Update result: " . json_encode($updateResult) . "\n";
