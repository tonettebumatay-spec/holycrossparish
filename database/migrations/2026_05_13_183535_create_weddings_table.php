<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('weddings', function (Blueprint $table) {
        $table->id();
        $table->string('category')->default('Wedding');
        $table->integer('book_number');
        $table->integer('page_number');
        $table->integer('line_number');
        $table->string('year');
        $table->string('month_day');

        // Groom Details
        $table->string('groom_name');
        $table->integer('groom_age');
        $table->string('groom_status');
        $table->string('groom_residence');
        $table->string('groom_parents');
        $table->string('groom_parents_residence');

        // Bride Details
        $table->string('bride_name');
        $table->integer('bride_age');
        $table->string('bride_status');
        $table->string('bride_residence');
        $table->string('bride_parents');
        $table->string('bride_parents_residence');

        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};