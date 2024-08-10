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
        Schema::create('parametre_analyse_processes', function (Blueprint $table) {
            $table->string('codePAP')->primary();
            $table->float('valeurMin');
            $table->float('valeurMax');
            $table->string('codeParam');
            $table->string('codeAP');
            $table->foreign('codeParam')->references('codeParam')->on('parametre_analyses')->onDelete('cascade');
            $table->foreign('codeAP')->references('codeAP')->on('analyse_processes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametre_analyse_processes');
    }
};
