<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tokens = [
    'bbb617dcfcf614ecfd3ee160ec3a873b',
    '148ed276a12b2dc4029cb38ad14d1c97',
    '7ba24da70e6a14e812bd365f2e28e370',
    'a16f5303507ad15887468129cadaa638'
];

foreach ($tokens as $token) {
    echo "Testing token: $token\n";
    try {
        $response = Illuminate\Support\Facades\Http::get('http://localhost/webservice/rest/server.php', [
            'wstoken' => $token,
            'wsfunction' => 'core_webservice_get_site_info',
            'moodlewsrestformat' => 'json'
        ]);
        $data = $response->json();
        if (isset($data['exception'])) {
            echo "  Result: Exception - " . $data['message'] . "\n";
        } elseif (isset($data['errorcode'])) {
            echo "  Result: Error - " . $data['errorcode'] . "\n";
        } else {
            echo "  Result: Valid token! User: " . $data['fullname'] . "\n";
        }
    } catch (\Exception $e) {
        echo "  Result: Request failed - " . $e->getMessage() . "\n";
    }
}
