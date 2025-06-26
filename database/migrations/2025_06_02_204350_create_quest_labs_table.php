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
        Schema::create('quest_labs', function (Blueprint $table) {
            $table->id();
            $table->string('note_by_provider_id');
            $table->string('appointment_uid');
            $table->string('test_name');
            $table->string('test_code');
            $table->string('category');
            $table->string('specimen_type');
            $table->string('urgency');
            $table->string('preferred_lab_location');
            $table->string('date');
            $table->string('time');
            $table->string('patient_name');
            $table->string('patient_id');
            $table->string('clinical_notes');
            $table->string('patient_phone_no');
            $table->string('insurance_provider');
            $table->string('estimated_cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_labs');
    }
};
