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
        Schema::create('it_service_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('it_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // frontliner
            $table->json('properties'); // { before: { field: value }, after: { field: value } }
            $table->boolean('is_approved')->nullable(); // null = pending
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_service_corrections');
    }
};
