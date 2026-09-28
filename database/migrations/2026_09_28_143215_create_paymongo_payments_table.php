<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paymongo_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('paymongo_id')->nullable(); // Payment Intent ID
            $table->string('checkout_session_id')->nullable();
            $table->string('reference_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PHP');
            $table->string('payment_method')->default('qrph'); // qrph
            $table->string('status')->default('pending'); // pending, paid, failed, expired
            $table->string('qr_code_url')->nullable();
            $table->string('payment_intent_status')->nullable();
            $table->text('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymongo_payments');
    }
};