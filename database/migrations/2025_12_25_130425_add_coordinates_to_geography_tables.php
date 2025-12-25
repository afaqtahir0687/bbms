<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = ['country', 'province', 'city', 'area'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable()->after('image_path');
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            });
        }
    }

    public function down()
    {
        $tables = ['country', 'province', 'city', 'area'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
