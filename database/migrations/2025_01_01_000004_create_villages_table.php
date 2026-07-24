<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandal_id')->constrained('mandals')->cascadeOnDelete();
            $table->string('name');           // e.g. "Hayathnagar"
            $table->string('slug');           // e.g. "hayathnagar"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mandal_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
