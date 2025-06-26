<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_forms', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('patients_name');
            $table->string('dob');
            $table->string('patients_signature')->nullable();
            $table->string('patients_dob')->nullable();
            $table->string('representative_signature')->nullable();
            $table->string('representative_dob')->nullable();
            $table->string('nature_with_patient')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_forms');
    }
};
