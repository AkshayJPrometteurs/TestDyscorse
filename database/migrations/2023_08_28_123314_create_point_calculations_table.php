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
        Schema::create('point_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('task_completion')->nullable();
            $table->string('max_streak')->nullable();
            $table->string('se_follow')->nullable();
            $table->string('se_assigning_task_to_family_member')->nullable();
            $table->string('feedback')->nullable();
            $table->string('app_sharing')->nullable();
            $table->string('reflection_and_review')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_calculations');
    }
};
