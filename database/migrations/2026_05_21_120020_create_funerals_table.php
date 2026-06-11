<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funerals', function (Blueprint $table) {
            $table->id();
            $table->string('reference_location')->nullable(); // Add this line - make it nullable
            $table->string('category')->nullable();
            $table->integer('book_number')->nullable();
            $table->integer('page_number')->nullable();
            $table->integer('line_number')->nullable();
            $table->string('deceased_name')->nullable();
            $table->string('residence')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('death_date')->nullable();
            $table->integer('age_at_death')->nullable();
            $table->date('burial_date')->nullable();
            $table->text('cause_of_death')->nullable();
            $table->string('sacraments_received')->nullable();
            $table->string('cemetery_name')->nullable();
            $table->string('minister_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funerals');
    }
};