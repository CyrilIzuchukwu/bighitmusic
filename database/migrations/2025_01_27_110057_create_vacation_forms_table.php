<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacation_forms', function (Blueprint $table) {
            $table->id();

            // Step 1: Personal details
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('occupation')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('dob');

            // Step 2: Residential details
            $table->text('address');
            $table->string('country_of_residence');

            // Step 3: Additional information
            $table->string('meeting_point');
            $table->string('airport');
            $table->date('vacation_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_forms');
    }
};
