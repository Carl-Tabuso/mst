<?php

use App\Models\Form3Hauling;
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
        Schema::create('form3_hauling_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Form3Hauling::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->boolean('is_vehicle_inspection_filled')->default(false);
            $table->boolean('is_uniform_ppe_filled')->default(false);
            $table->boolean('is_tools_equipment_filled')->default(false);
            $table->boolean('is_certify')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form3_hauling_checklists');
    }
};
