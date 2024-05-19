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
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('campus_name', 100);
            $table->string('majors', 100);
            $table->string('degree', 100);
            $table->unsignedBigInteger('fee_1');
            $table->unsignedBigInteger('fee_2');
            $table->unsignedBigInteger('fee_3');
            $table->unsignedBigInteger('fee_4');
            $table->unsignedBigInteger('fee_5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
