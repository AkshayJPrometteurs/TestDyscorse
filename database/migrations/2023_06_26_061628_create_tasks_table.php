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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->string('assign_task_type')->nullable();
            $table->string('day')->nullable();
            $table->string('wake_up_time')->nullable();
            $table->string('sleep_time')->nullable();
            $table->string('task_type')->nullable();
            $table->string('task_name')->nullable();
            $table->string('task_start_time')->nullable();
            $table->string('task_end_time')->nullable();
            $table->string('task_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
