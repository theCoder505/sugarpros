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
        Schema::create('claims_biller_form_data', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_uid');
            $table->string('name');
            $table->string('dob');
            $table->string('patient_id');
            $table->string('gender');
            $table->string('phone');
            $table->string('address');
            $table->string('coverage_type');
            $table->string('primary');
            $table->string('plan_name');
            $table->string('plan_type');
            $table->string('insurance_ID');
            $table->string('group_ID');
            $table->string('effective_date');
            $table->string('eligibility');
            $table->string('claim_address');
            $table->string('gurantor');
            $table->text('modifiers');
            $table->text('billing_code');
            $table->text('billing_text');
            $table->text('diagnoses_code');
            $table->text('diagnoses_text');
            $table->text('start_date');
            $table->text('end_date');
            $table->text('units');
            $table->text('quantity');
            $table->text('billed_charge');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims_biller_form_data');
    }
};
