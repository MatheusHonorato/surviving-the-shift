<?php

declare(strict_types=1);

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
        Schema::table('patient_metrics', function (Blueprint $table) {
            $table->unsignedInteger('hardest_step_index')->nullable()->after('hardest_step_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_metrics', function (Blueprint $table) {
            $table->dropColumn('hardest_step_index');
        });
    }
};
