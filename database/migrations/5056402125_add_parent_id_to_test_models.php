<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('test_models', function (Blueprint $table) {
            $table->foreignId('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('test_models', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
