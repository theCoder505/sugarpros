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
        Schema::create('privacy_forms', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('fname');
            $table->string('lname');
            $table->string('date');
            $table->text('users_message');
            $table->string('notice_of_privacy_practice');
            $table->string('patients_name')->nullable();
            $table->string('representatives_name')->nullable();
            $table->string('service_taken_date')->nullable();
            $table->text('relation_with_patient');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_forms');
    }
};
