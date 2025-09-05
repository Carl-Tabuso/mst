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
        Schema::create('form5_items', function (Blueprint $table) {
            $table->id();
             $table->foreignId('form5_id')
                ->constrained('form5')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
                   $table->string('item_name');      
                     $table->integer('quantity');      
            $table->timestamps();
    

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form5_items');
    }
};
