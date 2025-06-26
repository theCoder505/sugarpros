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
        Schema::create('e_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('note_by_provider_id');
            $table->string('appointment_uid');
            $table->string('patient_name');
            $table->string('patient_id');
            $table->string('age');
            $table->string('gender');
            $table->string('allergies');
            $table->string('drug_name');
            $table->string('strength');
            $table->string('form_manufacturer');
            $table->string('dose_amount');
            $table->string('frequency');
            $table->string('time_duration');
            $table->string('quantity');
            $table->string('refills');
            $table->string('start_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_prescriptions');
    }
};
