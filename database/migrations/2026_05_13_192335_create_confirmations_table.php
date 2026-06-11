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
    Schema::create('confirmations', function (Blueprint $table) {
        $table->id();
        $table->integer('line_number'); 
        $table->integer('book_number');
        $table->integer('page_number');
        $table->string('year');         
        $table->string('month_day');    
        
        // Child's Details
        $table->string('first_name');
        $table->string('last_name');
        $table->integer('age');         
        $table->string('birthplace');  
        
        // Parents' Details
        $table->string('father_name');
        $table->string('mother_name');
        $table->string('parents_residence'); 
        
        // Church Details
        $table->text('sponsors');
        $table->string('minister_name');
        $table->timestamps();
    });
}
};
