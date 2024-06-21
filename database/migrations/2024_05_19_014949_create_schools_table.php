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
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_name', 100);
            $table->integer('npsn');
            $table->string('school_email', 255)->unique();
            $table->string('password', 255)->nullable();
            $table->smallInteger('province')->unsigned();
            $table->smallInteger('city')->unsigned();
            $table->string('address', 255);
            $table->string('counselor_name', 100);
            $table->string('contact', 15);
            $table->enum('role', ['school']);
            $table->enum('payment_status', ['n', 'y']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
