<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('patients');
$missing = [];

if (!in_array('oas_score', $columns)) {
    Schema::table('patients', function ($table) {
        $table->string('oas_score')->nullable();
    });
    $missing[] = 'oas_score';
}

if (!in_array('diagnosis', $columns)) {
    Schema::table('patients', function ($table) {
        $table->string('diagnosis')->nullable();
    });
    $missing[] = 'diagnosis';
}

if (!in_array('smiv_group', $columns)) {
    Schema::table('patients', function ($table) {
        $table->json('smiv_group')->nullable();
    });
    $missing[] = 'smiv_group';
}

// Add other missing columns as needed
$other_cols = [
    'symp_mind',
    'symp_med',
    'symp_care',
    'symp_job',
    'symp_env',
    'symp_drug',
    'substances',
    'last_visit_date',
    'next_appointment_date',
    'cid',
    'address',
    'moo',
    'tambon',
    'amphoe',
    'area',
    'prefix'
];

Schema::table('patients', function ($table) use ($columns, &$missing, $other_cols) {
    foreach ($other_cols as $col) {
        if (!in_array($col, $columns)) {
            if ($col === 'substances') $table->json($col)->nullable();
            elseif (str_contains($col, 'date')) $table->date($col)->nullable();
            elseif ($col === 'cid') $table->string($col, 13)->nullable()->unique();
            else $table->string($col)->nullable();
            $missing[] = $col;
        }
    }
    // Fix existing 'hn' column to be nullable if it exists
    if (in_array('hn', $columns)) {
        try {
            $table->string('hn')->nullable()->change();
        } catch (\Exception $e) {
        }
    }
});

// Follow Ups
$follow_cols = Schema::getColumnListing('follow_ups');
$new_follow_cols = [
    'diagnosis',
    'smiv_group',
    'oas_score',
    'substances',
    'symp_mind',
    'symp_med',
    'symp_care',
    'symp_job',
    'symp_env',
    'symp_drug',
    'visit_status',
    'visit_reason',
    'visit_date',
    'appointment_plan',
    'next_appointment_date'
];

Schema::table('follow_ups', function ($table) use ($follow_cols, &$missing, $new_follow_cols) {
    foreach ($new_follow_cols as $col) {
        if (!in_array($col, $follow_cols)) {
            if (in_array($col, ['smiv_group', 'substances'])) $table->json($col)->nullable();
            elseif (str_contains($col, 'date')) $table->date($col)->nullable();
            else $table->string($col)->nullable();
            $missing[] = "follow_ups.$col";
        }
    }
});

echo "Fixed columns: " . implode(", ", $missing);
echo "\nDone";
