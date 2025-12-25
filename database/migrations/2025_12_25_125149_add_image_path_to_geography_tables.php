<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('country_name');
        });
        Schema::table('province', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('province_name');
        });
        Schema::table('city', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('city_name');
        });
        Schema::table('area', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('area_name');
        });
    }

    public function down()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
        Schema::table('province', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
        Schema::table('city', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
        Schema::table('area', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
