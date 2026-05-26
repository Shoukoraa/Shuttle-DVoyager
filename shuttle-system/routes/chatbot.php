<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::post('/login-dev', function () {
    $customerRole = \App\Models\Role::where('name', 'customer')->first();
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'role_id' => $customerRole ? $customerRole->id : 3,
            'phone' => '081234567890',
        ]
    );

    $token = $user->createToken('dev-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user
    ]);
});

Route::middleware('bearer.auth')->group(function () {
    // Chatbot Master Data
    Route::get('/chatbot/categories', [ChatController::class, 'getCategories']);
    Route::get('/chatbot/problems', [ChatController::class, 'getProblems']);

    // Live Admin Chat Sessions
    Route::post('/chat/connect-admin', [ChatController::class, 'connectAdmin']);
    Route::post('/chat/{sessionId}/messages', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{sessionId}/history', [ChatController::class, 'getHistory']);
    Route::post('/chat/{sessionId}/resolve', [ChatController::class, 'resolve']);
});
