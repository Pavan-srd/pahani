<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bhu_bharathis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mandal_id')->constrained('mandals')->restrictOnDelete();
            $table->foreignId('village_id')->constrained('villages')->restrictOnDelete();

            $table->string('module', 150);
            $table->string('application_number', 100);

            // PDF stored on Cloudflare R2 (uploaded directly from the browser)
            $table->string('file_name')->nullable();          // original file name
            $table->string('file_path', 500)->nullable();     // R2 object key
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->string('disk', 20)->default('r2');

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('uploaded_ip', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['mandal_id', 'village_id']);
            // Uniqueness of application_number is enforced in the controller
            // (ignoring soft-deleted rows), so a plain index is used here.
            $table->index('application_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhu_bharathis');
    }
};