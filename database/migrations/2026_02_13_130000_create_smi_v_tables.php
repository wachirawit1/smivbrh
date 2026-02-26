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
            $table->string('prefix')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cid', 13)->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('hn')->nullable()->unique()->index();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('moo')->nullable();
            $table->string('tambon')->nullable();
            $table->string('amphoe')->nullable();
            $table->string('area');
            $table->string('diagnosis')->nullable();
            $table->json('smiv_group')->nullable();
            $table->string('oas_score')->nullable();
            $table->string('symp_mind')->nullable();
            $table->string('symp_med')->nullable();
            $table->string('symp_care')->nullable();
            $table->string('symp_job')->nullable();
            $table->string('symp_env')->nullable();
            $table->string('symp_drug')->nullable();
            $table->json('substances')->nullable();
            $table->date('last_visit_date')->nullable();
            $table->date('next_appointment_date')->nullable();
            $table->string('status')->default('ติดตามปกติ');
            $table->timestamps();
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('diagnosis')->nullable();
            $table->json('smiv_group')->nullable();
            $table->string('oas_score')->nullable();
            $table->string('symp_mind')->nullable();
            $table->string('symp_med')->nullable();
            $table->string('symp_care')->nullable();
            $table->string('symp_job')->nullable();
            $table->string('symp_env')->nullable();
            $table->string('symp_drug')->nullable();
            $table->json('substances')->nullable();
            $table->string('visit_status')->nullable();
            $table->string('visit_reason')->nullable();
            $table->date('visit_date')->nullable();
            $table->string('appointment_plan')->nullable();
            $table->date('next_appointment_date')->nullable();
            $table->text('details')->nullable();
            $table->string('staff_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('patients');
    }
};
