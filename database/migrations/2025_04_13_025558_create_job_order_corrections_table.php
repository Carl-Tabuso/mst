<?php

use App\Enums\JobOrderCorrectionRequestStatus;
use App\Models\JobOrder;
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
        Schema::create('job_order_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobOrder::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('status')->default(JobOrderCorrectionRequestStatus::Pending);
            $table->json('properties'); // before and after
            $table->timestamp('approved_at')->nullable();
            $table->text('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_corrections');
    }
};
