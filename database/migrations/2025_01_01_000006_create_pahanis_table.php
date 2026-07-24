<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pahanis', function (Blueprint $table) {
            $table->id();

            // Location
            $table->foreignId('mandal_id')->constrained('mandals')->restrictOnDelete();
            $table->foreignId('village_id')->constrained('villages')->restrictOnDelete();

            // Which document (FK to lookup table)
            $table->foreignId('pahani_document_id')->constrained('pahani_documents')->restrictOnDelete();

            // Denormalised for quick queries / display without joins
            $table->string('document_name');          // "Sethwar Pahani" / "1985-86"
            $table->enum('type', ['core', 'year']);   // "core" / "year"

            // Physical availability
            $table->enum('physical_document', ['yes', 'no']);

            // File info — NULL when physical_document = 'no'
            $table->string('file_name')->nullable();       // original uploaded name
            $table->string('file_path')->nullable();       // R2 object key / path
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('file_mime')->nullable();       // "application/pdf"
            $table->string('disk')->nullable()->default('r2'); // storage disk used

            // Audit
            $table->string('uploaded_by')->nullable();     // user name / ID
            $table->ipAddress('uploaded_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // A village should have only one record per document type
            $table->unique(['village_id', 'pahani_document_id'], 'pahanis_village_doc_unique');

            // Useful query indexes
            $table->index(['mandal_id', 'village_id']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pahanis');
    }
};
