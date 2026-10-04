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
        Schema::table('bhu_bharathis', function (Blueprint $table) {
            $table->index(
                ['mandal_id', 'village_id', 'module_id', 'application_number'],
                'bhu_bharathis_combo_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bhu_bharathis', function (Blueprint $table) {
            $table->dropIndex('bhu_bharathis_combo_index');
        });
    }
};
