<?php

use App\Services\MoodleApiService;

Route::get('/test/create-user', function (MoodleApiService $api) {
    try {
        $username = 'testuser_' . time();
        echo "Creating user: $username\n";
        
        $userId = $api->createUser(
            $username,
            "$username@test.com",
            'Test',
            'User'
        );
        
        return response()->json([
            'status' => 'success',
            'userId' => $userId,
            'username' => $username,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 400);
    }
});
