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
                // Using longText for compatibility, or json if supported
                $table->longText('boundary_data')->nullable()->after('longitude');
            });
        }
    }

    public function down()
    {
        $tables = ['country', 'province', 'city', 'area'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('boundary_data');
            });
        }
    }
};
