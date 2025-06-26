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
        Schema::create('sugarpros_ai_chats', function (Blueprint $table) {
            $table->id();
            $table->string('requested_by')->default('patient');
            $table->string('requested_to')->default('AI');
            $table->string('message_of_uid');
            $table->longText('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugarpros_ai_chats');
    }
};
