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
        Schema::table('it_services', function (Blueprint $table) {
            $table->dropColumn([
                'machine_type',
                'model',
                'serial_no',
                'tag_no',
                'machine_problem',
                'service_performed',
                'recommendation',
                'machine_status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_services', function (Blueprint $table) {
            $table->string('machine_type');
            $table->string('model');
            $table->string('serial_no');
            $table->string('tag_no');
            $table->longText('machine_problem')->nullable();
            $table->longText('service_performed')->nullable();
            $table->longText('recommendation')->nullable();
            $table->string('machine_status')->nullable();
        });
    }
};
