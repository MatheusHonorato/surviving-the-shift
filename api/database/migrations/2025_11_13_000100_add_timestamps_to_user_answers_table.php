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
        Schema::table('answers', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('is_correct');
            $table->timestamp('answered_at')->nullable()->after('started_at');
            $table->index(['user_id', 'step_id', 'answered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'step_id', 'answered_at']);
            $table->dropColumn(['started_at', 'answered_at']);
        });
    }
};
