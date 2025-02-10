<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('referral_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('referral_id')->nullable(false)->change();
        });
    }
};
