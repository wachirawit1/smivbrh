<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable(); // Still keep age for easy querying, but calculated
            $table->string('gender')->nullable();
            $table->string('hn')->unique()->index();
            $table->string('phone')->nullable();
            $table->string('area'); // Responsible area
            $table->string('severity')->default('green'); // green, yellow, orange, red, purple
            $table->string('relative_assessment')->nullable();
            $table->text('details')->nullable();
            $table->date('last_follow_up')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->string('status')->default('รอติดตาม'); // จำหน่าย, ติดตามปกติ
            $table->timestamps();
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('result'); // พบตัว, ไม่พบตัว, เสียชีวิต, อื่นๆ
            $table->string('tracking_status')->nullable(); // จำหน่ายเฉพาะผู้ป่วย
            $table->string('drug_status')->nullable(); // ไม่ขาดยา, ขาดยา, อื่นๆ
            $table->string('referral_hospital')->nullable();
            $table->text('referral_details')->nullable();
            $table->json('triggers')->nullable(); // Alcohol, Drugs, etc.
            $table->string('relative_assessment')->nullable();
            $table->text('details')->nullable();
            $table->string('staff_name')->nullable();
            $table->date('follow_up_date');
            $table->date('next_follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('patients');
    }
};
