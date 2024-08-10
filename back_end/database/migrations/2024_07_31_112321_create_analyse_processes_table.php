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
        Schema::create('analyse_processes', function (Blueprint $table) {
            $table->id();
            $table->string('codeAP')->unique();
            $table->string('codeEtape');
            $table->string('codePlan');
            $table->string('codeAnalyse');
            $table->foreign('codeEtape')->references('codeEtape')->on('etape_processes')->onDelete('cascade');
            $table->foreign('codePlan')->references('codePlan')->on('plan_controles')->onDelete('cascade');
            $table->foreign('codeAnalyse')->references('codeAnalyse')->on('analyses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyse_processes');
    }
};
