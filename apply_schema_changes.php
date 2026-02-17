<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Starting manual schema update...\n";

// PATIENTS TABLE
if (Schema::hasTable('patients')) {
    echo "Checking 'patients' table...\n";
    Schema::table('patients', function (Blueprint $table) {
        // Drop unique index on hn if exists (might fail if not exists, so careful)
        // We'll skip dropping for now to avoid errors, just focus on adding columns.

        // Add columns if missing
        if (!Schema::hasColumn('patients', 'diagnosis')) {
            $table->string('diagnosis')->nullable();
            echo "Added 'diagnosis' to patients.\n";
        }

        if (!Schema::hasColumn('patients', 'smiv_group')) {
            $table->json('smiv_group')->nullable();
            echo "Added 'smiv_group' to patients.\n";
        } else {
            // Check type? Hard to do easily. Assume it's ok or handled.
        }

        if (!Schema::hasColumn('patients', 'oas_score')) {
            $table->string('oas_score')->nullable();
            echo "Added 'oas_score' to patients.\n";
        }

        // Symptom fields
        foreach (['symp_mind', 'symp_med', 'symp_care', 'symp_job', 'symp_env', 'symp_drug'] as $col) {
            if (!Schema::hasColumn('patients', $col)) {
                $table->string($col)->nullable();
                echo "Added '$col' to patients.\n";
            }
        }

        if (!Schema::hasColumn('patients', 'substances')) {
            $table->json('substances')->nullable();
            echo "Added 'substances' to patients.\n";
        }

        if (!Schema::hasColumn('patients', 'last_visit_date')) {
            $table->date('last_visit_date')->nullable();
            echo "Added 'last_visit_date' to patients.\n";
        }

        if (!Schema::hasColumn('patients', 'next_appointment_date')) {
            $table->date('next_appointment_date')->nullable();
            echo "Added 'next_appointment_date' to patients.\n";
        }

        if (!Schema::hasColumn('patients', 'cid')) {
            $table->string('cid', 13)->nullable()->unique();
            echo "Added 'cid' to patients.\n";
        }
    });
} else {
    echo "Table 'patients' does not exist!\n";
}

// FOLLOW_UPS TABLE
if (Schema::hasTable('follow_ups')) {
    echo "Checking 'follow_ups' table...\n";
    Schema::table('follow_ups', function (Blueprint $table) {
        if (!Schema::hasColumn('follow_ups', 'diagnosis')) {
            $table->string('diagnosis')->nullable();
            echo "Added diagnosis to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'smiv_group')) {
            $table->json('smiv_group')->nullable();
            echo "Added smiv_group to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'oas_score')) {
            $table->string('oas_score')->nullable();
            echo "Added oas_score to follow_ups\n";
        }

        foreach (['symp_mind', 'symp_med', 'symp_care', 'symp_job', 'symp_env', 'symp_drug'] as $col) {
            if (!Schema::hasColumn('follow_ups', $col)) {
                $table->string($col)->nullable();
                echo "Added $col to follow_ups\n";
            }
        }

        if (!Schema::hasColumn('follow_ups', 'substances')) {
            $table->json('substances')->nullable();
            echo "Added substances to follow_ups\n";
        }

        if (!Schema::hasColumn('follow_ups', 'visit_status')) {
            $table->string('visit_status')->nullable();
            echo "Added visit_status to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'visit_reason')) {
            $table->string('visit_reason')->nullable();
            echo "Added visit_reason to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'visit_date')) {
            $table->date('visit_date')->nullable();
            echo "Added visit_date to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'appointment_plan')) {
            $table->string('appointment_plan')->nullable();
            echo "Added appointment_plan to follow_ups\n";
        }
        if (!Schema::hasColumn('follow_ups', 'next_appointment_date')) {
            $table->date('next_appointment_date')->nullable();
            echo "Added next_appointment_date to follow_ups\n";
        }
    });
}

echo "Manual schema update completed.\n";
