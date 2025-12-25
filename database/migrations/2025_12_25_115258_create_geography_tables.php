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
        Schema::create('country', function (Blueprint $table) {
            $table->id('country_id');
            $table->string('country_code', 50)->unique();
            $table->string('country_name');
        });

        Schema::create('province', function (Blueprint $table) {
            $table->id('province_id');
            $table->foreignId('country_id')->constrained('country', 'country_id');
            $table->string('province_code', 50)->unique();
            $table->string('province_name');
        });

        Schema::create('city', function (Blueprint $table) {
            $table->id('city_id');
            $table->foreignId('province_id')->constrained('province', 'province_id');
            $table->string('city_code', 50)->unique();
            $table->string('city_name');
        });

        Schema::create('area', function (Blueprint $table) {
            $table->id('area_id');
            $table->foreignId('city_id')->constrained('city', 'city_id');
            $table->string('area_code', 50)->unique();
            $table->string('area_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area');
        Schema::dropIfExists('city');
        Schema::dropIfExists('province');
        Schema::dropIfExists('country');
    }
};
