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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('working_office_id')
                ->nullable()
                ->after('is_admin')
                ->constrained('working_offices')
                ->nullOnDelete();

            $table->tinyInteger('status')
                ->default(0)
                ->after('working_office_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('working_office_id');
            $table->dropColumn('status');
        });
    }
};
