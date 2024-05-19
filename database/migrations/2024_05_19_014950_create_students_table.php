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
            $table->unsignedBigInteger('school_id')->nullable();
            $table->enum('school_status', ['accepted', 'rejected', 'pending']);
            $table->string('student_name', 100)->nullable();
            $table->string('final_score', 100);
            $table->string('student_email', 100)->unique();
            $table->string('password', 100);
            $table->string('birth_date', 100)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('contact', 100)->nullable();
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
