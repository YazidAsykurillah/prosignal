<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_intelligences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->json('industries')->nullable();
            $table->json('roles')->nullable();
            $table->json('company_sizes')->nullable();
            $table->json('opportunity_signals')->nullable();
            $table->json('discovery_keywords')->nullable();
            $table->longText('raw_ai_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_intelligences');
    }
};
