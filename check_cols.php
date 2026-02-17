<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('patients');
echo "Columns in patients table:\n";
print_r($columns);

$hasHn = Schema::hasColumn('patients', 'hn');
echo "Has 'hn' column: " . ($hasHn ? 'Yes' : 'No') . "\n";
echo "Has 'cid' column: " . (Schema::hasColumn('patients', 'cid') ? 'Yes' : 'No') . "\n";
