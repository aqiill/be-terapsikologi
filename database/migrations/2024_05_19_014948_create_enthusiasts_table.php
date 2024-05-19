<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enthusiasts', function (Blueprint $table) {
            $table->id();
            $table->string('campus_id', 100);
            $table->string('year', 100);
            $table->integer('capacity');
            $table->integer('applicants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enthusiasts');
    }
};
