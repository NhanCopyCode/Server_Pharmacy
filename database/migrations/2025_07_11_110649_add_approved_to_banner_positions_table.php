<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banner_positions', function (Blueprint $table) {
            $table->boolean('approved')->default(1)->after('name');
        });
    }

    public function down()
    {
        Schema::table('banner_positions', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
};
