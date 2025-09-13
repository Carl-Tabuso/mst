<?php

use App\Models\JobOrder;
use App\Models\Form3Hauling; // ADD THIS IMPORT
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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('form3_hauling_id') 
                ->nullable()
                ->constrained('form3_haulings')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('created_by')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamp('occured_at');
            $table->longText('description');
            $table->string('subject');
            $table->string('location');
            $table->string('infraction_type');
            $table->boolean('is_read')->default(false);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};