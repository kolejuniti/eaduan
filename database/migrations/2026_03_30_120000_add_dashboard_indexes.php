<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damage_complaints', function (Blueprint $table) {
            $table->index('created_at', 'damage_complaints_created_at_idx');
        });

        Schema::table('general_complaints', function (Blueprint $table) {
            $table->index('created_at', 'general_complaints_created_at_idx');
            $table->index('status_id', 'general_complaints_status_id_idx');
        });

        Schema::table('damage_complaint_logs', function (Blueprint $table) {
            $table->index(['damage_complaint_id', 'id'], 'damage_complaint_logs_complaint_id_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('damage_complaints', function (Blueprint $table) {
            $table->dropIndex('damage_complaints_created_at_idx');
        });

        Schema::table('general_complaints', function (Blueprint $table) {
            $table->dropIndex('general_complaints_created_at_idx');
            $table->dropIndex('general_complaints_status_id_idx');
        });

        Schema::table('damage_complaint_logs', function (Blueprint $table) {
            $table->dropIndex('damage_complaint_logs_complaint_id_id_idx');
        });
    }
};
