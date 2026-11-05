<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marriage_banns', function (Blueprint $table) {
            // Groom Details
            $table->string('groom_dob')->nullable();
            $table->string('groom_pob')->nullable();
            $table->string('groom_father')->nullable();
            $table->string('groom_mother')->nullable();
            $table->text('groom_address')->nullable();
            $table->string('groom_picture')->nullable();

            // Bride Details
            $table->string('bride_dob')->nullable();
            $table->string('bride_pob')->nullable();
            $table->string('bride_father')->nullable();
            $table->string('bride_mother')->nullable();
            $table->text('bride_address')->nullable();
            $table->string('bride_picture')->nullable();

            // Publication Dates
            $table->date('publication_start')->nullable();
            $table->date('publication_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('marriage_banns', function (Blueprint $table) {
            $table->dropColumn([
                'groom_dob', 'groom_pob', 'groom_father', 'groom_mother', 'groom_address', 'groom_picture',
                'bride_dob', 'bride_pob', 'bride_father', 'bride_mother', 'bride_address', 'bride_picture',
                'publication_start', 'publication_end'
            ]);
        });
    }
};