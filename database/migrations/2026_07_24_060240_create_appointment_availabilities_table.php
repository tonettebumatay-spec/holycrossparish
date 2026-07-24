<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointment_availabilities', function (Blueprint $table) {
            $table->id();
            $table->enum('sacrament_type', ['baptism', 'communion', 'confirmation', 'wedding', 'funeral']);
            $table->date('available_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_slots')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // 👇 Use a shorter unique index name
           $table->unique(
    ['sacrament_type', 'available_date', 'start_time', 'end_time'],
    'avail_unique'  // Short name
);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_availabilities');
    }
};