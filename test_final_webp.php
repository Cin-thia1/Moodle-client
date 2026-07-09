<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Tester avec un fichier webp
$webpFile = storage_path('app/public/profile_pictures/TTSpri8cx9RVZiOd8Uu4SjRx55DC0PWzcKF2mSpM.webp');
echo "Fichier webp: $webpFile\n";
echo "Existe: " . (file_exists($webpFile) ? 'oui' : 'non') . "\n";

$service = app(\App\Services\MoodleUserService::class);
$result = $service->updateUserPicture(2, $webpFile);
echo "Résultat updateUserPicture: " . ($result ? 'SUCCÈS ✅' : 'ÉCHEC ❌') . "\n";

echo "\nDernières lignes du log:\n";
$logLines = array_slice(file(storage_path('logs/laravel.log')), -10);
echo implode('', $logLines);
