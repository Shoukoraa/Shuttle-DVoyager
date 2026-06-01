<?php 
require __DIR__.'/../shuttle-app/vendor/autoload.php'; 
$app = require_once __DIR__.'/../shuttle-app/bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class); 
$kernel->handle(Illuminate\Http\Request::capture()); 
\Illuminate\Support\Facades\Artisan::call('optimize:clear'); 
echo 'CACHE BERHASIL DIBERSIHKAN OLEH SYSTEM!'; 
?>
