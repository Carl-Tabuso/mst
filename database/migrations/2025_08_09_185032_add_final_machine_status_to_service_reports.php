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
        Schema::table('it_service_reports', function (Blueprint $table) {
            $table->string('final_machine_status')->nullable()->after('final_remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_service_reports', function (Blueprint $table) {
            $table->dropColumn('final_machine_status');
        });
    }
};
