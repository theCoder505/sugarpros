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
        Schema::create('virtual_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_by_provider_id');
            $table->string('appointment_uid');
            $table->string('patient_id');
            $table->string('main_note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_notes');
    }
};
