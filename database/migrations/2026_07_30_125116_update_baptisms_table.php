<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communions', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });

        Schema::table('confirmations', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });

        Schema::table('weddings', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });

        Schema::table('funerals', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('communions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('confirmations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('funerals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};