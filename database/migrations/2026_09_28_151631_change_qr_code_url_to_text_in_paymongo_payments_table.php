<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paymongo_payments', function (Blueprint $table) {
            $table->text('qr_code_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('paymongo_payments', function (Blueprint $table) {
            $table->string('qr_code_url')->nullable()->change();
        });
    }
};