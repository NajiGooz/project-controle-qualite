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
            $table->foreignId('codeEtape')->constrained('etape_processes');
            $table->foreignId('codePlan')->constrained('plan_controles');
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
