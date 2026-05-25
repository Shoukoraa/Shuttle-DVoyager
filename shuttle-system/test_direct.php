<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$token = '5|wmfp3hxF6xaQcS0i0ZYgaFh0zInS1LbsT3JpQVPb55eb6e78';

// Create a dummy image
$imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
file_put_contents(__DIR__ . '/dummy.png', $imageContent);

$request = Illuminate\Http\Request::create(
    '/api/test-auth',
    'GET',
    [],
    [],
    [],
    [
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        'HTTP_ACCEPT' => 'application/json',
    ]
);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null) {
        echo "SHUTDOWN ERROR:\n";
        print_r($error);
    }
});

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "CAUGHT FATAL ERROR:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
