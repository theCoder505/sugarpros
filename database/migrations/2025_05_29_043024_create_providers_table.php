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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id')->unique();
            $table->string('name');
            $table->string('prefix_code')->nullable();
            $table->string('mobile');
            $table->string('email')->unique();
            $table->string('forget_otp')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('password');
            $table->string('profile_picture')->nullable();
            $table->string('language')->default('en');
            $table->boolean('hippa_consent')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
