<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('certificate_id')->nullable();
            $table->string('payment_method'); // cash, gcash
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('reference_number')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};