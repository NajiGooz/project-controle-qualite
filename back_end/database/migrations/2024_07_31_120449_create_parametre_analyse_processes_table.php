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
            $table->id('codePAP'); // Identifiant unique
            $table->float('valeurMin');
            $table->float('valeurMax');
            $table->foreignId('codeParam')->constrained('parametre_analyses'); // Assurez-vous également ici
            $table->foreignId('codeAP')->constrained('analyse_processes'); // Assurez-vous que les types correspondent
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
