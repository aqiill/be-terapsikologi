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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('school_id')->nullable(); // Corrected definition
            $table->tinyInteger('o')->unsigned()->default(0);
            $table->tinyInteger('total_o');
            $table->tinyInteger('ce')->unsigned()->default(0);
            $table->tinyInteger('total_ce');
            $table->tinyInteger('ea')->unsigned()->default(0);
            $table->tinyInteger('total_ea');
            $table->tinyInteger('an')->unsigned()->default(0);
            $table->tinyInteger('total_an');
            $table->tinyInteger('n')->unsigned()->default(0);
            $table->tinyInteger('total_n');
            $table->tinyInteger('r')->unsigned()->default(0);
            $table->tinyInteger('total_r');
            $table->tinyInteger('i')->unsigned()->default(0);
            $table->tinyInteger('total_i');
            $table->tinyInteger('a')->unsigned()->default(0);
            $table->tinyInteger('total_a');
            $table->tinyInteger('s')->unsigned()->default(0);
            $table->tinyInteger('total_s');
            $table->tinyInteger('e')->unsigned()->default(0);
            $table->tinyInteger('total_e');
            $table->tinyInteger('c')->unsigned()->default(0);
            $table->tinyInteger('total_c');
            $table->tinyInteger('math')->unsigned()->default(0);
            $table->tinyInteger('total_math');
            $table->tinyInteger('visual')->unsigned()->default(0);
            $table->tinyInteger('total_visual');
            $table->tinyInteger('memory')->unsigned()->default(0);
            $table->tinyInteger('total_memory');
            $table->tinyInteger('reading')->unsigned()->default(0);
            $table->tinyInteger('total_reading');
            $table->tinyInteger('induction')->unsigned()->default(0);
            $table->tinyInteger('total_induction');
            $table->tinyInteger('quantitative_reasoning')->unsigned()->default(0);
            $table->tinyInteger('total_quantitative_reasoning');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
