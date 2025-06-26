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
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_by_provider_id');
            $table->string('appointment_uid');
            $table->string('chief_complaint');
            $table->string('history_of_present_illness');
            $table->string('past_medical_history');
            $table->string('medications');
            $table->string('family_history');
            $table->string('social_history');
            $table->string('physical_examination');
            $table->string('assessment_plan');
            $table->string('progress_notes');
            $table->text('provider_information');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
