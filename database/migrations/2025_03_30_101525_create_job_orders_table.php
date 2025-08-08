<?php

use App\Models\Employee;
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
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->morphs('serviceable');
            $table->timestamp('date_time');
            $table->string('client');
            $table->text('address');
            $table->string('department');
            $table->string('contact_no');
            $table->string('contact_person');
            $table->string('contact_position');
            $table->foreignIdFor(Employee::class, 'created_by')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('status');
            $table->unsignedInteger('error_count')->default(0);
            $table->timestamps();
            $table->softDeletes('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
