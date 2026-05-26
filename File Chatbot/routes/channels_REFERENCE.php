<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return true; // Simplified for local testing and demo
});

Broadcast::channel('admin.chat', function ($user) {
    return true; // Simplified for local testing and demo
});
