<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('sacrament_type'); // baptism, communion, confirmation, wedding, funeral
            $table->string('requirement_name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('sacrament_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_requirements');
    }
};