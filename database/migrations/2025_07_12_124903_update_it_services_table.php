<?php

use App\Models\Employee;
use App\Enums\ITServiceStatus;
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
        Schema::table('it_services', function (Blueprint $table) {
            // Drop old fields now handled in a separate report table
            $table->dropColumn([
                'service_performed',
                'recommendation',
                'machine_status',
            ]);
        });

        Schema::table('it_services', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class, 'technician_id')
                ->nullable()
                ->after('machine_problem')
                ->constrained()
                ->nullOnDelete();

            $table->string('status')->default(ITServiceStatus::ForCheckUp->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_services', function (Blueprint $table) {
            $table->dropColumn([
                'technician_id',
                'status',
            ]);

            $table->longText('service_performed')->nullable();
            $table->longText('recommendation')->nullable();
            $table->string('machine_status')->nullable();
        });
    }
};
