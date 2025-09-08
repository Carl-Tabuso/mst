<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->unique();
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('pagibig_number')->nullable();
            $table->string('tin')->nullable();
            $table->date('date_hired');
            $table->date('regularization_date')->nullable();
            $table->date('end_of_contract')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_employment_details');
    }
};
