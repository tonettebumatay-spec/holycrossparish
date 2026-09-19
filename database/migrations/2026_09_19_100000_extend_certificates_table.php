<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('certificates', 'appointment_date')) {
                $table->date('appointment_date')->nullable()->after('certificate_type');
            }
            if (!Schema::hasColumn('certificates', 'appointment_time')) {
                $table->time('appointment_time')->nullable()->after('appointment_date');
            }
            if (!Schema::hasColumn('certificates', 'details')) {
                $table->text('details')->nullable()->after('appointment_time');
            }
            if (!Schema::hasColumn('certificates', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('details');
            }
            if (!Schema::hasColumn('certificates', 'email')) {
                $table->string('email')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('certificates', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('certificates', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'contact_number',
                'appointment_date',
                'appointment_time',
                'details',
                'user_id',
                'email',
                'cancellation_reason',
                'is_locked',
            ]);
        });
    }
};