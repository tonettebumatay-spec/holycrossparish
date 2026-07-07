<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('viewings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->longText('image'); // base64 encoded image
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('viewings');
    }
};