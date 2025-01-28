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
        Schema::create('private_vacations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('phone')->nullable();
            $table->string('occupation')->nullable();
            $table->date('date_of_birth');
            $table->text('house_address')->nullable();
            $table->string('country_of_residence')->nullable();
            $table->string('meeting_point');
            $table->string('airport')->nullable();
            $table->date('vacation_date');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_vacations');
    }
};
