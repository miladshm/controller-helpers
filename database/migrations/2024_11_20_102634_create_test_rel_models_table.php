<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_rel_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_model_id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_rel_models');
    }
};
