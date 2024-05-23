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
            $table->string('school_id', 100)->nullable();
            $table->string('o', 100);
            $table->integer('total_o');
            $table->string('ce', 100);
            $table->integer('total_ce');
            $table->string('ea', 100);
            $table->integer('total_ea');
            $table->string('an', 100);
            $table->integer('total_an');
            $table->string('n', 100);
            $table->integer('total_n');
            $table->string('r', 100);
            $table->integer('total_r');
            $table->string('i', 100);
            $table->integer('total_i');
            $table->string('a', 100);
            $table->integer('total_a');
            $table->string('s', 100);
            $table->integer('total_s');
            $table->string('e', 100);
            $table->integer('total_e');
            $table->string('c', 100);
            $table->integer('total_c');
            $table->string('math', 100);
            $table->integer('total_math');
            $table->string('visual', 100);
            $table->integer('total_visual');
            $table->string('memory', 100);
            $table->integer('total_memory');
            $table->string('reading', 100);
            $table->integer('total_reading');
            $table->string('induction', 100);
            $table->integer('total_induction');
            $table->string('quantitative_reasoning', 100);
            $table->integer('total_quantitative_reasoning');
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
