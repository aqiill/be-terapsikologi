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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('school_id')->nullable();
            $table->enum('school_status', ['accepted', 'rejected', 'pending']);
            $table->string('student_name', 100)->nullable();
            $table->tinyInteger('final_score');
            $table->string('student_email', 255)->unique();
            $table->string('password', 255);
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->smallInteger('province')->nullable();
            $table->smallInteger('city')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('contact', 15)->nullable();
            $table->enum('payment_status', ['n', 'y']);
            $table->enum('recommendation_type', ['kemdikbud', 'kemenag', 'poltekkes', 'kedinasan', 'multiple']);
            $table->enum('role', ['student']);
            $table->timestamps();

            // $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
