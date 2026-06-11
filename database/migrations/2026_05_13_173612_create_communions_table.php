<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('communions', function (Blueprint $table) {
        $table->id();
        $table->integer('book_number');
        $table->integer('page_number'); 
        $table->integer('line_number'); 
        $table->string('first_name');
        $table->string('last_name');
        $table->date('communion_date');
        $table->string('residence');    
        $table->string('minister_name');
        $table->date('baptism_date');   
        $table->string('place_of_baptism');
        $table->timestamps();
    });
}
};
