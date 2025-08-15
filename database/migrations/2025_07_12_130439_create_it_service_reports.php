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
        Schema::create('it_service_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('it_service_id')
                ->constrained()
                ->cascadeOnDelete();

            // Distinguish between first and second onsite visit
            $table->enum('onsite_type', ['Initial', 'Final']);

            // Common fields
            $table->longText('service_performed')->nullable(); // can be reused for both onsites
            $table->longText('recommendation')->nullable();    // relevant to first onsite
            $table->string('machine_status')->nullable();      // status from either visit
            $table->string('attached_file')->nullable();       // technician file for first onsite

            // Only relevant in second onsite
            $table->longText('parts_replaced')->nullable();
            $table->longText('final_remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_service_reports');
    }
};
