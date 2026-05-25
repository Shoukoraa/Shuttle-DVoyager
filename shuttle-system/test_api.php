<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Get first customer user
$user = User::with('role')->where('role_id', 3)->first(); // role_id 3 = customer

if (!$user) {
    echo "❌ No customer user found in database\n";
    exit;
}

echo "✅ User found: " . $user->name . " (" . $user->email . ")\n";
echo "✅ User role: " . ($user->role ? $user->role->name : 'NO ROLE') . "\n";

// Create a test token
$token = $user->createToken('test-token')->plainTextToken;
echo "✅ Token created: " . substr($token, 0, 20) . "...\n\n";

// Test /api/me endpoint
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer $token\r\n",
    ]
]);

echo "Testing GET /api/me...\n";
$response = @file_get_contents('http://localhost:8000/api/me', false, $context);

if ($response === false) {
    echo "❌ Request failed\n";
    if (isset($http_response_header)) {
        echo "Response header: " . implode(', ', $http_response_header) . "\n";
    }
} else {
    echo "✅ Response:\n";
    echo $response . "\n";
}
