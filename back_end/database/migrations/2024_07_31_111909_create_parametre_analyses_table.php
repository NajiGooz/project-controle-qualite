<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parametre_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('codeParam')->unique();
            $table->string('libelleParam');
            $table->foreignId('codeAnalyse')->constrained('analyses');
            $table->foreignId('codeUnite')->constrained('unite_mesures');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametre_analyses');
    }
};
