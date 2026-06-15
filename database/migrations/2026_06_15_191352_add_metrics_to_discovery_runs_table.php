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
        Schema::table('discovery_runs', function (Blueprint $table) {
            $table->integer('urls_found')->default(0)->after('total_companies');
            $table->integer('urls_filtered')->default(0)->after('urls_found');
            $table->integer('urls_analyzed')->default(0)->after('urls_filtered');
            $table->integer('companies_found')->default(0)->after('urls_analyzed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discovery_runs', function (Blueprint $table) {
            $table->dropColumn(['urls_found', 'urls_filtered', 'urls_analyzed', 'companies_found']);
        });
    }
};
