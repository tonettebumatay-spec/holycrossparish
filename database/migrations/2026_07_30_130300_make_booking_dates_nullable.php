<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->date('baptism_date')->nullable()->change();
        });

        Schema::table('communions', function (Blueprint $table) {
            $table->date('communion_date')->nullable()->change();
            $table->date('baptism_date')->nullable()->change();
        });

        Schema::table('confirmations', function (Blueprint $table) {
            $table->string('month_day')->nullable()->change();
        });

        Schema::table('weddings', function (Blueprint $table) {
            $table->string('month_day')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->date('baptism_date')->nullable(false)->change();
        });

        Schema::table('communions', function (Blueprint $table) {
            $table->date('communion_date')->nullable(false)->change();
            $table->date('baptism_date')->nullable(false)->change();
        });

        Schema::table('confirmations', function (Blueprint $table) {
            $table->string('month_day')->nullable(false)->change();
        });

        Schema::table('weddings', function (Blueprint $table) {
            $table->string('month_day')->nullable(false)->change();
        });
    }
};