<?php

use App\Models\ITService;
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
        Schema::create('initial_onsite_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ITService::class, 'it_service_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->longText('service_performed');
            $table->longText('recommendation');
            $table->string('machine_status');
            $table->string('file_name');
            $table->string('file_hash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_onsite_reports');
    }
};
