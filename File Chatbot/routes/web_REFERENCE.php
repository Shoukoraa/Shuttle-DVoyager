<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat-demo', function () {
    // Auto-login seeded dev user for easy testing
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]
    );
    Auth::login($user);

    return view('chat_demo');
});

Route::get('/admin/active-sessions', function () {
    return \App\Models\ChatSession::with(['user', 'lastCategory'])
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();
});
