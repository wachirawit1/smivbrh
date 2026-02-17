<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix PostgreSQL JSON column issue
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE patients ALTER COLUMN smiv_group TYPE json USING smiv_group::json');
        }

        // For MySQL/SQLite
        Schema::table('patients', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'pgsql') {
                $table->json('smiv_group')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // Revert if needed
    }
};
