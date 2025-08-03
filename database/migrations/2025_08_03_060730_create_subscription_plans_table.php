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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('availed_by_uid');
            $table->string('recurring_option');
            $table->string('plan');
            $table->string('users_full_name');
            $table->string('users_address');
            $table->string('users_email');
            $table->string('users_phone');
            $table->string('country_code');
            $table->string('amount');
            $table->string('stripe_charge_id');
            $table->date('last_recurrent_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
