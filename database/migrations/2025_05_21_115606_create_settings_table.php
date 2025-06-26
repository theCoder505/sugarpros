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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('brandname');
            $table->string('brandlogo');
            $table->string('brandicon');
            $table->string('currency');
            $table->string('contact_email');
            $table->string('stripe_amount');
            $table->string('stripe_key');
            $table->string('stripe_secret');
            $table->string('subscription_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
