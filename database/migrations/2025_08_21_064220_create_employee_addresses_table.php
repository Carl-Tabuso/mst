<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->unique();
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('zip_code');
            $table->text('detailed_address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_addresses');
    }
};
