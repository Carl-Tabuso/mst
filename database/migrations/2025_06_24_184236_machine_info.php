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
        Schema::create('machine_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('it_service_id')->constrained('it_services')->cascadeOnDelete();
            $table->string('machine_type');
            $table->string('model');
            $table->string('serial_no');
            $table->string('tag_no');
            $table->longText('machine_problem')->nullable();
            $table->longText('service_performed')->nullable();
            $table->longText('recommendation')->nullable();
            $table->string('machine_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_infos');
    }
};
