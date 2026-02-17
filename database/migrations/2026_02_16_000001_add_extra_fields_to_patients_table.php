<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'prefix')) {
                $table->string('prefix')->nullable()->after('id');
            }
            if (!Schema::hasColumn('patients', 'smiv_group')) {
                $table->string('smiv_group')->nullable()->after('severity'); // กลุ่ม 1, 2, 3, 4
            }
            if (!Schema::hasColumn('patients', 'address')) {
                $table->string('address')->nullable();
                $table->string('moo')->nullable();
                $table->string('tambon')->nullable();
                $table->string('amphoe')->nullable();
            }
            if (!Schema::hasColumn('patients', 'chronic_disease')) {
                $table->json('chronic_disease')->nullable();
            }
            if (!Schema::hasColumn('patients', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['prefix', 'smiv_group', 'address', 'moo', 'tambon', 'amphoe', 'chronic_disease', 'latitude', 'longitude']);
        });
    }
};
