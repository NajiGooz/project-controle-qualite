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
            $table->foreignId('codeEtape')->constrained('etape_processes')->onDelete('cascade');
            $table->foreignId('codePlan')->constrained('plan_controles')->onDelete('cascade');
            $table->foreignId('analyse_id')->constrained('analyses')->onDelete('cascade');
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
