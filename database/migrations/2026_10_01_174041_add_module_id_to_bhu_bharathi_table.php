<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links each disposal to a Module master record.
 * The existing `module` text column is kept as the module name (kept in sync
 * when an admin renames a module), so existing rows and reports keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bhu_bharathis', function (Blueprint $table) {
            $table->foreignId('module_id')
                ->nullable()
                ->after('village_id')
                ->constrained('modules')
                ->restrictOnDelete();   // a module in use cannot be deleted
        });
    }

    public function down(): void
    {
        Schema::table('bhu_bharathis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_id');
        });
    }
};