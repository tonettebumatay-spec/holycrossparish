<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baptisms', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->integer('book_number');
            $table->integer('page_number'); // Added for BK/PG/LN tracking
            $table->integer('line_number'); // Added for BK/PG/LN tracking
            $table->string('first_name');
            $table->string('last_name');
            $table->string('legitimacy');
            $table->date('birth_date'); // The column previously causing the error
            $table->string('birth_place');
            $table->string('father_name');
            $table->string('father_birthplace')->nullable();
            $table->string('mother_maiden_name');
            $table->string('mother_birthplace')->nullable();
            $table->string('residence');
            $table->date('baptism_date');
            $table->string('minister_name');
            $table->string('godfather')->nullable();
            $table->string('godmother')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baptisms');
    }
};