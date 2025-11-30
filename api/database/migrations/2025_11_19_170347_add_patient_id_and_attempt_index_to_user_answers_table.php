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
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->after('user_id')->constrained('patients')->onDelete('cascade');
            $table->unsignedInteger('attempt')->nullable()->after('patient_id');
            $table->index(['user_id', 'patient_id', 'attempt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'patient_id', 'attempt']);
            $table->dropColumn(['patient_id', 'attempt']);
        });
    }
};
