<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_models', function (Blueprint $table) {
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_models', function (Blueprint $table) {
            $table->dropColumn(['status', 'order']);
        });
    }
};
