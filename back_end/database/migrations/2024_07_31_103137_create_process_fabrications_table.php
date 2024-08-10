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
        Schema::create('process_fabrications', function (Blueprint $table) {
            $table->integer('orderEtape');
            $table->string('codeEtape');
            $table->string('codePlan');
            $table->foreign('codeEtape')->references('codeEtape')->on('etape_processes')->onDelete('cascade');
            $table->foreign('codePlan')->references('codePlan')->on('plan_controles')->onDelete('cascade');
            $table->primary(['codeEtape', 'codePlan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_fabrications');
    }
};
