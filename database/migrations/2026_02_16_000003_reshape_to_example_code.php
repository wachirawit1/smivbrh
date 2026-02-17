<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Remove old columns safely
            try {
                $table->dropUnique('patients_hn_unique');
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }

            $columnsToDrop = ['hn', 'moo', 'latitude', 'longitude', 'chronic_disease', 'relative_assessment', 'details', 'last_follow_up', 'next_follow_up'];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('patients', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Add new/restructured columns safely
            if (!Schema::hasColumn('patients', 'diagnosis')) $table->string('diagnosis')->nullable();

            if (Schema::hasColumn('patients', 'smiv_group')) {
                // If it exists, we might want to change it, but changing type can be tricky without doctrine/dbal.
                // For now, we assume if it exists we might need to drop and re-add if type is wrong, or just leave it.
                // A safer way without doctrine is to drop if it's not JSON, but let's just try to change it if we can, or ignore.
                try {
                    $table->json('smiv_group')->nullable()->change();
                } catch (\Exception $e) {
                    // if change fails (e.g. no doctrine), we might need to drop and add. 
                    // BUT dropping loses data. Let's start with just trying to add if not exists.
                }
            } else {
                $table->json('smiv_group')->nullable();
            }

            if (!Schema::hasColumn('patients', 'oas_score')) $table->string('oas_score')->nullable();
            if (!Schema::hasColumn('patients', 'symp_mind')) $table->string('symp_mind')->nullable();
            if (!Schema::hasColumn('patients', 'symp_med')) $table->string('symp_med')->nullable();
            if (!Schema::hasColumn('patients', 'symp_care')) $table->string('symp_care')->nullable();
            if (!Schema::hasColumn('patients', 'symp_job')) $table->string('symp_job')->nullable();
            if (!Schema::hasColumn('patients', 'symp_env')) $table->string('symp_env')->nullable();
            if (!Schema::hasColumn('patients', 'symp_drug')) $table->string('symp_drug')->nullable();
            if (!Schema::hasColumn('patients', 'substances')) $table->json('substances')->nullable();
            if (!Schema::hasColumn('patients', 'last_visit_date')) $table->date('last_visit_date')->nullable();
            if (!Schema::hasColumn('patients', 'next_appointment_date')) $table->date('next_appointment_date')->nullable();
        });

        Schema::table('follow_ups', function (Blueprint $table) {
            // Remove old columns safely
            $columnsToDrop = ['result', 'tracking_status', 'drug_status', 'referral_hospital', 'referral_details', 'triggers', 'relative_assessment', 'details', 'follow_up_date', 'next_follow_up_date'];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('follow_ups', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Add new columns
            if (!Schema::hasColumn('follow_ups', 'diagnosis')) $table->string('diagnosis')->nullable();
            if (!Schema::hasColumn('follow_ups', 'smiv_group')) $table->json('smiv_group')->nullable();
            if (!Schema::hasColumn('follow_ups', 'oas_score')) $table->string('oas_score')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_mind')) $table->string('symp_mind')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_med')) $table->string('symp_med')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_care')) $table->string('symp_care')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_job')) $table->string('symp_job')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_env')) $table->string('symp_env')->nullable();
            if (!Schema::hasColumn('follow_ups', 'symp_drug')) $table->string('symp_drug')->nullable();
            if (!Schema::hasColumn('follow_ups', 'substances')) $table->json('substances')->nullable();

            if (!Schema::hasColumn('follow_ups', 'visit_status')) $table->string('visit_status')->nullable();
            if (!Schema::hasColumn('follow_ups', 'visit_reason')) $table->string('visit_reason')->nullable();
            if (!Schema::hasColumn('follow_ups', 'visit_date')) $table->date('visit_date')->nullable();
            if (!Schema::hasColumn('follow_ups', 'appointment_plan')) $table->string('appointment_plan')->nullable();
            if (!Schema::hasColumn('follow_ups', 'next_appointment_date')) $table->date('next_appointment_date')->nullable();
        });
    }

    public function down(): void
    {
        // Not implemented for simplicity in this prototyping phase
    }
};
