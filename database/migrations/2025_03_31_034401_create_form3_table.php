<?php

use App\Models\Employee;
use App\Models\Form4;
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
        Schema::create('form3', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Form4::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamp('approved_date')->nullable();
            $table->string('truck_no')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('appraised_date')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->foreignIdFor(Employee::class, 'team_leader')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Employee::class, 'team_driver')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Employee::class, 'safety_officer')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Employee::class, 'team_mechanic')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form3');
    }
};
