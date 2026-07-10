<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\MoodleApiService::class);

// Tester pour chaque user: est-ce que contextid = 5 (token owner) ou dépend de l'user?
// core_files_get_unused_draft_itemid crée un draft pour le TOKEN OWNER uniquement.
// core_user_update_picture n'a pas de param contextid, seulement userid et draftitemid.
// Donc le draft est toujours créé dans le contexte du token owner (admin) = contextid 5
// Et Moodle utilise userid pour savoir qui on met à jour.
// Ça marche si l'admin peut mettre à jour n'importe quel user.

// Le vrai problème: le format webp n'est pas supporté.
// Testons d'autres formats
$formats = [
    '.webp' => base64_decode('UklGRlYAAABXRUJQVlA4IEoAAADQAQCdASoBAAEABUB8JZQCdAEO/g3OAAAA'), // small webp
];

foreach ($formats as $ext => $data) {
    $tmpFile = sys_get_temp_dir() . '/test' . $ext;
    file_put_contents($tmpFile, $data);
    echo "Format: $ext, size: " . filesize($tmpFile) . ", mime: " . mime_content_type($tmpFile) . "\n";
    
    $draftInfo = $api->call('core_files_get_unused_draft_itemid', []);
    $uploadResult = $api->callPost('core_files_upload', [
        'contextid' => $draftInfo['contextid'],
        'component' => 'user',
        'filearea' => 'draft',
        'itemid' => $draftInfo['itemid'],
        'filepath' => '/',
        'filename' => 'profile' . $ext,
        'filecontent' => base64_encode($data),
    ]);
    echo "Upload: " . json_encode($uploadResult) . "\n";
    
    $updateResult = $api->call('core_user_update_picture', [
        'userid' => 2,
        'draftitemid' => $draftInfo['itemid'],
    ]);
    echo "Update: " . json_encode($updateResult) . "\n\n";
}

// Le webp semble retourner success=true mais sans vraiment mettre à jour (l'url reste la default).
// Regardons l'URL retournée - si c'est "theme/image.php" c'est la photo par défaut.
// Si c'est "pluginfile.php" c'est une vraie photo.
// Test avec un vrai JPEG cette fois
$testFile = storage_path('app/public/profile_pictures/VzNxpOG7CWBxkuQPaZ6VE9QToRPI9TfCZD2LXrdC.jpg');
$draftInfo = $api->call('core_files_get_unused_draft_itemid', []);
$api->callPost('core_files_upload', [
    'contextid' => $draftInfo['contextid'],
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $draftInfo['itemid'],
    'filepath' => '/',
    'filename' => 'profile.jpg',
    'filecontent' => base64_encode(file_get_contents($testFile)),
]);
$r = $api->call('core_user_update_picture', ['userid' => 2, 'draftitemid' => $draftInfo['itemid']]);
echo "JPEG test: " . json_encode($r) . "\n";
// URL "pluginfile.php" = vraie photo. "theme/image.php" = photo par défaut = échec

