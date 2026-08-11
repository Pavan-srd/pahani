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
            $table->json('upload_mandal_ids')->nullable()->after('can_edit');
            $table->json('view_mandal_ids')->nullable()->after('upload_mandal_ids');
            $table->json('edit_mandal_ids')->nullable()->after('view_mandal_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_document_permissions', function (Blueprint $table) {
            $table->dropColumn(['upload_mandal_ids', 'view_mandal_ids', 'edit_mandal_ids']);
        });
    }
};
