<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bhu Bharathi module upload permission: which modules (from the `modules`
 * table) a user may upload disposals for. Stored on the same
 * UserDocumentPermission row as the mandal permissions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_document_permissions', function (Blueprint $table) {
            $table->json('bb_upload_module_ids')->nullable()->after('bb_edit_mandal_ids');
        });
    }

    public function down(): void
    {
        Schema::table('user_document_permissions', function (Blueprint $table) {
            $table->dropColumn('bb_upload_module_ids');
        });
    }
};