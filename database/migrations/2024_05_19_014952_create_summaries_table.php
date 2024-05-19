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
            $table->string('school_id', 100);
            $table->string('o', 100);
            $table->string('ce', 100);
            $table->string('ea', 100);
            $table->string('an', 100);
            $table->string('n', 100);
            $table->string('r', 100);
            $table->string('i', 100);
            $table->string('a', 100);
            $table->string('s', 100);
            $table->string('e', 100);
            $table->string('c', 100);
            $table->string('math', 100);
            $table->string('visual', 100);
            $table->string('memory', 100);
            $table->string('reading', 100);
            $table->string('induction', 100);
            $table->string('quantitative_reasoning', 100);
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
