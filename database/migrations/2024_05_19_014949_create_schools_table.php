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
            $table->id();
            $table->string('school_name', 100);
            $table->integer('npsn');
            $table->string('school_email', 100)->unique();
            $table->string('password', 100)->nullable();
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('address', 100);
            $table->string('operator_name', 100);
            $table->string('contact', 100);
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
