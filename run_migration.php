<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "Starting migration...\n";
$exitCode = Artisan::call('migrate', ['--force' => true]);
echo "Migration finished with code: " . $exitCode . "\n";
echo Artisan::output();
