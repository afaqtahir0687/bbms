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
        Schema::create('customer', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('customer_code', 50)->unique();
            $table->string('customer_name');
        });

        Schema::create('campaign', function (Blueprint $table) {
            $table->id('campaign_id');
            $table->foreignId('customer_id')->constrained('customer', 'customer_id');
            $table->string('campaign_code', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 50);

            $table->index(['start_date', 'end_date']);
        });

        Schema::create('soft_booking', function (Blueprint $table) {
            $table->id('soft_booking_id');
            $table->foreignId('campaign_id')->constrained('campaign', 'campaign_id');
            $table->foreignId('billboard_id')->constrained('billboard', 'billboard_id');
            $table->dateTime('hold_from');
            $table->dateTime('hold_to');
            $table->string('hold_status', 50);
            $table->dateTime('expires_at')->nullable();

            $table->index(['billboard_id', 'hold_from', 'hold_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soft_booking');
        Schema::dropIfExists('campaign');
        Schema::dropIfExists('customer');
    }
};
