<?php

require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

try {
    $email = "if24.jonatansimbolon@mhs.ubpkarawang.ac.id";
    $user = User::where("email", $email)->first();

    if (!$user) {
        die("❌ User not found\n");
    }

    echo "✅ User found: " . $user->name . " (" . $user->email . ")\n";
    echo "✅ User role: " . $user->role->name . "\n";

    $token = $user->createToken("auth_token")->plainTextToken;
    echo "✅ Token created: " . substr($token, 0, 20) . "...\n";

    echo "\nTesting GET /api/me via manual Controller call...\n";
    
    $request = Illuminate\Http\Request::create("/api/me", "GET");
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $controller = new App\Http\Controllers\AuthController();
    $response = $controller->getUserProfile($request);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
