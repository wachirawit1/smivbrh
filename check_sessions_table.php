<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exists = Illuminate\Support\Facades\Schema::hasTable('sessions');
    echo "Sessions table exists: " . ($exists ? "YES" : "NO") . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
