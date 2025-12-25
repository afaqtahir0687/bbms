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
        Schema::create('allocation', function (Blueprint $table) {
            $table->id('allocation_id');
            $table->foreignId('campaign_id')->constrained('campaign', 'campaign_id');
            $table->foreignId('billboard_id')->constrained('billboard', 'billboard_id');
            $table->date('allocated_from');
            $table->date('allocated_to');
            $table->string('allocation_status', 50);

            $table->index(['billboard_id', 'allocated_from', 'allocated_to']);
        });

        Schema::create('allocation_uploader_assignment', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->foreignId('allocation_id')->constrained('allocation', 'allocation_id')->onDelete('cascade');
            $table->foreignId('uploader_user_id')->constrained('app_user', 'user_id');
            $table->boolean('active_flag')->default(true);
            $table->dateTime('assigned_on');

            $table->unique(['allocation_id', 'uploader_user_id'], 'uk_aua_alloc_user');
        });

        Schema::create('picture', function (Blueprint $table) {
            $table->id('picture_id');
            $table->foreignId('allocation_id')->constrained('allocation', 'allocation_id')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('app_user', 'user_id');
            $table->dateTime('uploaded_at');
            $table->string('file_path', 1024);
            $table->string('picture_status', 50);
        });

        Schema::create('verification', function (Blueprint $table) {
            $table->id('verification_id');
            $table->foreignId('picture_id')->unique()->constrained('picture', 'picture_id')->onDelete('cascade');
            $table->foreignId('verified_by')->constrained('app_user', 'user_id');
            $table->dateTime('verified_at');
            $table->string('result', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification');
        Schema::dropIfExists('picture');
        Schema::dropIfExists('allocation_uploader_assignment');
        Schema::dropIfExists('allocation');
    }
};
