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
        Schema::create('majors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classification', 100);
            $table->string('field', 100);
            $table->string('major', 100);
            $table->tinyInteger('o')->unsigned()->default(0);
            $table->tinyInteger('ce')->unsigned()->default(0);
            $table->tinyInteger('ea')->unsigned()->default(0);
            $table->tinyInteger('an')->unsigned()->default(0);
            $table->tinyInteger('n')->unsigned()->default(0);
            $table->tinyInteger('r')->unsigned()->default(0);
            $table->tinyInteger('i')->unsigned()->default(0);
            $table->tinyInteger('a')->unsigned()->default(0);
            $table->tinyInteger('s')->unsigned()->default(0);
            $table->tinyInteger('e')->unsigned()->default(0);
            $table->tinyInteger('c')->unsigned()->default(0);
            $table->tinyInteger('math')->unsigned()->default(0);
            $table->tinyInteger('visual')->unsigned()->default(0);
            $table->tinyInteger('memory')->unsigned()->default(0);
            $table->tinyInteger('reading')->unsigned()->default(0);
            $table->tinyInteger('induction')->unsigned()->default(0);
            $table->tinyInteger('quantitative_reasoning')->unsigned()->default(0);
            $table->text('major_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
