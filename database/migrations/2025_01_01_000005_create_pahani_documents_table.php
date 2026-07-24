<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pahani_documents', function (Blueprint $table) {
            $table->id();
            // e.g. "sethwar", "kasra", "sessala", "yr_1960", "yr_1961" … "yr_2024"
            $table->string('value')->unique();
            // e.g. "Sethwar Pahani", "Kasra Pahani", "1960-61", "1961-62" …
            $table->string('label');
            // "core" or "year"
            $table->enum('type', ['core', 'year']);
            // Telugu description — NULL for year records
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pahani_documents');
    }
};
