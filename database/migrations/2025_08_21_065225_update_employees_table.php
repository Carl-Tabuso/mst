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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->after('suffix');
            $table->string('email')->after('date_of_birth');
            $table->string('contact_number')->after('email');

            $table->string('job_assignment');

            if (! $this->isSqlite()) {
                $table->dropIndex('employees_first_name_middle_name_last_name_suffix_fulltext');
            }

            if (! $this->isSqlite()) {
                $table->fullText([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                    'email',
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'email',
                'contact_number',
                'job_assignment',
            ]);

            if (! $this->isSqlite()) {
                $table->dropIndex('employees_first_name_middle_name_last_name_suffix_email_fulltext');
                $table->fullText([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                ]);
            }
        });
    }

    private function isSqlite(): bool
    {
        return Schema::connection($this->getConnection())
            ->getConnection()
            ->getPdo()
            ->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
    }
};
