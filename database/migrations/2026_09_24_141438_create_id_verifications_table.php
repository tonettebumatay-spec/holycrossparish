<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->string('id_type'); // driver_license, national_id, tin_id, voters_id, philhealth_id
            $table->string('id_number')->nullable();
            $table->string('full_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('address')->nullable();
            $table->string('id_picture_path')->nullable();
            $table->string('status')->default('verified');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_verifications');
    }
};