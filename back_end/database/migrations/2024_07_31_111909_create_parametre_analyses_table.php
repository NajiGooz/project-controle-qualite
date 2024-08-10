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
            $table->string('codeAnalyse');
            $table->string('codeUnite');
            $table->foreign('codeAnalyse')->references('codeAnalyse')->on('analyses')->onDelete('cascade');
            $table->foreign('codeUnite')->references('codeUnite')->on('unite_mesures')->onDelete('cascade');
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
