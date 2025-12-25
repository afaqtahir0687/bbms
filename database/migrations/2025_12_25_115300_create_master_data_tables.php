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
        Schema::create('rating_scale', function (Blueprint $table) {
            $table->id('rating_id');
            $table->string('rating_code', 50)->unique();
            $table->string('rating_value', 50);
        });

        Schema::create('area_market_rating', function (Blueprint $table) {
            $table->foreignId('area_id')->primary()->constrained('area', 'area_id')->onDelete('cascade');
            $table->foreignId('rating_id')->constrained('rating_scale', 'rating_id');
            $table->text('rationale')->nullable();
        });

        Schema::create('billboard_type', function (Blueprint $table) {
            $table->id('billboard_type_id');
            $table->string('type_code', 50)->unique();
            $table->string('type_name');
        });

        Schema::create('billboard', function (Blueprint $table) {
            $table->id('billboard_id');
            $table->foreignId('area_id')->constrained('area', 'area_id');
            $table->string('billboard_code', 50)->unique();
            $table->string('display_name');
            $table->foreignId('billboard_type_id')->constrained('billboard_type', 'billboard_type_id');
            $table->foreignId('market_rating_id')->nullable()->constrained('rating_scale', 'rating_id')->onDelete('set null');
            $table->string('status', 50);
            $table->boolean('active_flag')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billboard');
        Schema::dropIfExists('billboard_type');
        Schema::dropIfExists('area_market_rating');
        Schema::dropIfExists('rating_scale');
    }
};
