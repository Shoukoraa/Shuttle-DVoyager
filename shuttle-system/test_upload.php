<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Let's create a fake user and token
$user = \App\Models\User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

$token = $user->createToken('test')->plainTextToken;
echo "Token: $token\n";
