<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{sessionId}', function ($user, $sessionId) {
    if ($user->role && $user->role->name === 'admin') {
        return true;
    }
    $session = \App\Models\ChatSession::find($sessionId);
    return $session && (int) $session->user_id === (int) $user->id;
});

Broadcast::channel('admin.chat', function ($user) {
    return $user->role && $user->role->name === 'admin';
});
