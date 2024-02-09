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
        Schema::create('task_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('task_category_name')->nullable();
            $table->string('task_sub_category_name')->unique()->nullable();
            $table->string('task_sub_category_slug')->nullable();
            $table->string('task_sub_category_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_sub_categories');
    }
};
