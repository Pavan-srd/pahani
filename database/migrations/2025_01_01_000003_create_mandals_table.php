<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandals', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. "Hayathnagar"
            $table->string('slug')->unique(); // e.g. "hayathnagar"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandals');
    }
};
