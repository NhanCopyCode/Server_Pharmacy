<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id')->nullable()->after('title');
            $table->foreign('position_id')->references('id')->on('banner_positions')->onDelete('set null');
        });
    }

    
    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
