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
        Schema::table('user_document_permissions', function (Blueprint $table) {
            $table->json('bb_upload_mandal_ids')->nullable()->after('edit_mandal_ids');
            $table->json('bb_view_mandal_ids')->nullable()->after('bb_upload_mandal_ids');
            $table->json('bb_edit_mandal_ids')->nullable()->after('bb_view_mandal_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_document_permissions', function (Blueprint $table) {
            $table->dropColumn(['bb_upload_mandal_ids', 'bb_view_mandal_ids', 'bb_edit_mandal_ids']);
        });
    }
};
