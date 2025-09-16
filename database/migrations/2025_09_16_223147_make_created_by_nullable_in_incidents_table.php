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
        Schema::table('incidents', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['created_by']);

            // Make the column nullable
            $table->foreignId('created_by')
                ->nullable()
                ->change();

            // Re-add the foreign key
            $table->foreign('created_by')
                ->references('id')
                ->on('employees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreignId('created_by')
                ->nullable(false)
                ->change();
            $table->foreign('created_by')
                ->references('id')
                ->on('employees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
