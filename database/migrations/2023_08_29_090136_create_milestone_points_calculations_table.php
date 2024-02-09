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
        Schema::create('milestone_points_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('milestone_start')->nullable();
            $table->string('milestone_end')->nullable();
            $table->string('milestone_points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestone_points_calculations');
    }
};
